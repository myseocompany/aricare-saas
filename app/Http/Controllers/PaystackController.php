<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Bill;
use App\Models\User;
use App\Models\IpdPayment;
use Laracasts\Flash\Flash;
use App\Models\Appointment;
use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\BillTransaction;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\IpdPatientDepartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Models\AppointmentTransaction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\AppointmentRepository;
use App\Repositories\MedicineBillRepository;
use App\Repositories\SubscriptionRepository;
use Unicodeveloper\Paystack\Facades\Paystack;
use App\Repositories\PurchaseMedicineRepository;
use App\Mail\NotifyMailSuperAdminForSubscribeHospital;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class PaystackController extends Controller
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * PaypalController constructor.
     */
    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function paystackConfig()
    {
        config([
            'paystack.publicKey' => getSuperAdminPaymentCredentials('paystack_key'),
            'paystack.secretKey' => getSuperAdminPaymentCredentials('paystack_secret'),
            'paystack.paymentUrl' => 'https://api.paystack.co',
        ]);
    }

    public function redirectToGateway($input)
    {

        $this->paystackConfig();

        $subscriptionsPricingPlan = SubscriptionPlan::findOrFail($input['plan']);

        $data = $this->subscriptionRepository->manageSubscription($input['plan']);

        if (!in_array(strtoupper(getCurrentCurrency()), payStackSupportedCurrencies())) {
            Notification::make()
                ->danger()
                ->title(__('messages.new_change.paystack_support_zar'))
                ->send();
            return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
        }

        if (! isset($data['plan'])) { // 0 amount plan or try to switch the plan if it is in trial mode
            // returning from here if the plan is free.
            if (isset($data['status']) && $data['status'] == true) {
                Notification::make()->title($data['subscriptionPlan']->name . ' ' . __('messages.subscription_pricing_plans.has_been_subscribed'))->success()->send();
                return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
            } else {
                if (isset($data['status']) && $data['status'] == false) {
                    Notification::make()->title(__('messages.flash.cannot_switch'))->danger()->send();
                    return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
                }
            }
        }

        $subscriptionsPricingPlan = $data['plan'];
        $subscription = $data['subscription'];

        try {
            $paystackData = [
                'email' => getLoggedInUser()->email,
                'orderID' => $subscription->id,
                'amount' => ($data['amountToPay'] * 100),
                'quantity' => 1,
                'currency' => strtoupper(getCurrentCurrency()),
                'reference' => Paystack::genTranxRef(),
                'metadata' => json_encode(['subscription_id' => $subscription->id]),
            ];

            $authorizationUrl = Paystack::getAuthorizationUrl($paystackData);

            $authorizationUrl =  $authorizationUrl->url;
            return redirect($authorizationUrl);
        } catch (\Exception $e) {
            dd($e->getMessage());
            Notification::make()->title(__('messages.new_change.payment_fail'))->danger()->send();
            return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
        }
    }

    public function handleGatewayCallback(Request $request)
    {
        $this->paystackConfig();
        $reference = $request->get('reference');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . getPaymentCredentials('paystack_secret_key'),
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");
            if (!$response->successful()) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . getSuperAdminPaymentCredentials('paystack_secret'),
                ])->get("https://api.paystack.co/transaction/verify/{$reference}");
            }

            if ($response->successful() && $response->json('data.status') === 'success') {
                $transactionID = $response->json('data.id');
                $originalResponse = $response->json('data');
                $response = $response->json('data.metadata');

                if (array_key_exists('is_patient_bill_payment', $response)) {
                    $amount = $response['amount'];
                    $billId = $response['id'];
                    $bill = Bill::find($billId);

                    if (!empty($bill)) {
                        BillTransaction::create([
                            'transaction_id' => $reference,
                            'payment_type' => BillTransaction::PAYSTACK,
                            'amount' => $amount,
                            'bill_id' => $bill->id,
                            'status' => 4,
                            'meta' => null,
                            'is_manual_payment' => 0,
                        ]);
                        $bill->update(['payment_mode' => BillTransaction::PAYSTACK, 'status' => '1']);

                        Notification::make()
                            ->title(__('messages.payment.your_payment_is_successfully_completed'))
                            ->success()
                            ->send();

                        return Redirect::route('filament.hospitalAdmin.billings.resources.bills.index');
                    }
                } elseif (array_key_exists('appointment_charge', $response)) {
                    $appointmentRepository = app(AppointmentRepository::class);
                    $response['payment_status'] = 1;
                    $data = $appointmentRepository->create($response);

                    $appointmentTransaction = AppointmentTransaction::create([
                        'transaction_id' => $transactionID,
                        'appointment_id' => $data->id,
                        'transaction_type' => $data->payment_type,
                        'amount' => $data->amount,
                        'tenant_id' => getLoggedInUser()->tenant_id
                    ]);

                    Notification::make()
                        ->title(__('messages.flash.appointment_saved'))
                        ->success()
                        ->send();
                    return redirect(route('filament.hospitalAdmin.appointment'));
                } else if (array_key_exists('ipd_patient_department_id', $response)) {

                    $ipdPayment = IpdPayment::create($response);

                    $ipdPatientDepartment = IpdPatientDepartment::find($ipdPayment->ipd_patient_department_id);
                    $ipdBill = $ipdPatientDepartment->bill;

                    if ($ipdBill) {
                        $amount = $ipdPayment->amount;
                        $ipdBill->total_payments = $ipdBill->total_payments + $amount;
                        $ipdBill->net_payable_amount = $ipdBill->net_payable_amount - $amount;
                        $ipdBill->save();
                    }

                    Notification::make()
                        ->title(__('messages.flash.IPD_payment_updated'))
                        ->success()
                        ->send();

                    return redirect(route('filament.hospitalAdmin.ipd-opd.resources.ipd-patients.index'));
                } else if (array_key_exists('saleMedicine', $response)) {
                    $medicineBillRepository = app(MedicineBillRepository::class);
                    $medicineBillRepository->paystackPaymentSuccess($response);

                    Notification::make()
                        ->title(__('messages.payment.your_payment_is_successfully_completed'))
                        ->success()
                        ->send();

                    return redirect(route('filament.hospitalAdmin.medicine.resources.medicine-bills.index'));
                } else if (array_key_exists('purchase_no', $response)) {
                    $purchaseMedicineRepository = app(PurchaseMedicineRepository::class);
                    $purchaseMedicineRepository->paystackPaymentSuccess($response);
                    Notification::make()
                        ->title(__('messages.payment.your_payment_is_successfully_completed'))
                        ->success()
                        ->send();

                    return redirect(route('filament.hospitalAdmin.medicine.resources.purchase-medicines.index'));
                } elseif (array_key_exists('type', $response) && $response['type'] == 'webAppointment') {
                    try {
                        DB::beginTransaction();

                        $appointmentTransaction = AppointmentTransaction::create([
                            'transaction_id' => $transactionID,
                            'appointment_id' => $response['data']['appointment_id'],
                            'transaction_type' => $response['data']['input']['payment_type'],
                            'amount' => $response['data']['amount'],
                            'tenant_id' => getLoggedInUser()->tenant_id
                        ]);


                        $appointment = Appointment::find($response['data']['appointment_id']);
                        $appointment->update(['payment_status' => 1, 'payment_type' => \App\Models\Appointment::PAYSTACK]);
                        DB::commit();

                        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

                        if (!getLoggedInUser()->hasRole('Admin')) {
                            $tenant = Auth::user()->tenant_id;
                            $user = User::whereTenantId($tenant)->whereNotNull('username')->first();
                        } else {
                            $user = Auth::user();
                        }

                        return redirect(route('appointment', ['username' => $user->username]));
                    } catch (Exception $e) {
                        DB::rollback();
                        $appointment = Appointment::orderBy('created_at', 'desc')->latest()->first();
                        $appointment->delete();
                        throw new UnprocessableEntityHttpException($e->getMessage());
                    }
                } elseif (array_key_exists('subscription_id', $response)) {
                    try {
                        $subscriptionId = $response['subscription_id'];
                        $subscriptionAmount = $originalResponse['amount'] / 100;
                        $transactionID = $transactionID;

                        Subscription::findOrFail($subscriptionId)->update(['status' => Subscription::ACTIVE]);

                        Subscription::whereUserId(getLoggedInUserId())
                            ->where('id', '!=', $subscriptionId)
                            ->update([
                                'status' => Subscription::INACTIVE,
                            ]);

                        $transaction = Transaction::create([
                            'transaction_id' => $transactionID,
                            'payment_type' => Transaction::TYPE_PAYSTACK,
                            'amount' => $subscriptionAmount,
                            'user_id' => getLoggedInUserId(),
                            'status' => Subscription::ACTIVE,
                            'meta' => json_encode($response),
                        ]);

                        // updating the transaction id on the subscription table
                        $subscription = Subscription::with('subscriptionPlan')->findOrFail($subscriptionId);
                        $subscription->update(['transaction_id' => $transaction->id]);

                        Flash::success($subscription->subscriptionPlan->name . ' ' . __('messages.subscription_pricing_plans.has_been_subscribed'));
                        $toastData = [
                            'toastType' => 'success',
                            'toastMessage' => $subscription->subscriptionPlan->name . ' ' . __('messages.subscription_pricing_plans.has_been_subscribed'),
                        ];

                        $mailData = [
                            'amount' => $subscriptionAmount,
                            'user_name' => getLoggedInUser()->full_name,
                            'plan_name' => $subscription->subscriptionPlan->name,
                            'start_date' => $subscription->starts_at,
                            'end_date' => $subscription->ends_at,
                        ];

                        Mail::to(getLoggedInUser()->email)
                            ->send(new NotifyMailSuperAdminForSubscribeHospital(
                                'emails.hospital_subscription_mail',
                                __('messages.new_change.subscription_mail'),
                                $mailData
                            ));

                        setPlanFeatures();

                        if (session('from_pricing') == 'landing.home') {
                            return redirect(route('landing-home'))->with('toast-data', $toastData);
                        } elseif (session('from_pricing') == 'landing.about.us') {
                            return redirect(route('landing.about.us'))->with('toast-data', $toastData);
                        } elseif (session('from_pricing') == 'landing.services') {
                            return redirect(route('landing.services'))->with('toast-data', $toastData);
                        } elseif (session('from_pricing') == 'landing.pricing') {
                            return redirect(route('landing.pricing'))->with('toast-data', $toastData);
                        } else {
                            Notification::make()
                                ->title($subscription->subscriptionPlan->name . ' ' . __('messages.subscription_pricing_plans.has_been_subscribed'))
                                ->success()
                                ->send();

                            return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
                        }
                    } catch (Exception $e) {
                        DB::rollback();
                        Notification::make()
                            ->title(__('messages.payment.payment_failed'))
                            ->danger()
                            ->send();

                        return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
                    }
                }
            } else {
                Notification::make()
                    ->title(__('messages.payment.payment_failed'))
                    ->danger()
                    ->send();

                return redirect()->route('filament.hospitalAdmin.pages.subscription-plans');
            }
        } catch (HttpException $ex) {
            Log::error($ex->getMessage());
            throw new UnprocessableEntityHttpException($ex->getMessage());
        }
    }
}
