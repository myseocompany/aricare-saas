<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Bancolombia\Wompi;

use Filament\Notifications\Notification;
use App\Actions\Subscription\CreateSubscription;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WompiController extends Controller
{
    public function purchase(Request $request)
    {
        try {
            $plan = json_decode($request->plan);

            $data = [
                'user_id' => Auth::id(),
                'plan_id' => $plan->id,
            ];

            session(['data' => $data]);

            $publicKey = getSuperAdminSettingKeyValue('wompi_public_key');
            $privateKey = getSuperAdminSettingKeyValue('wompi_private_key');
            $env = getSuperAdminSettingKeyValue('wompi_env');

            $reference = uniqid('wompi_');

            $transactionData = [
                'amount_in_cents'   => (int) ($plan->payable_amount * 100),
                'currency'          => strtoupper($plan->currency->code ?? 'COP'),
                'customer_email'    => Auth::user()->email,
                'reference'         => $reference,
                'redirect_url'      => route('wompi.success'),
            ];

            $transaction = Wompi::createTransaction($transactionData, $privateKey, $env, $publicKey);

            if (isset($transaction['data']['redirect_url'])) {
                return redirect($transaction['data']['redirect_url']);
            }

            Notification::make()
                ->danger()
                ->title(__('messages.payment.payment_failed'))
                ->send();

            return redirect(route('filament.hospitalAdmin.pages.subscription-plans'));
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title($e->getMessage())
                ->send();

            return redirect(route('filament.hospitalAdmin.pages.subscription-plans'));
        }
    }

    public function success(Request $request)
    {
        $payload = $request->all();
        $data = session('data');
        $plan = SubscriptionPlan::find($data['plan_id']);

        $eventSecret = getSuperAdminSettingKeyValue('wompi_events_secret');
        $providedSignature = $request->header('X-Signature') ?? ($payload['signature'] ?? null);

        $calculatedSignature = hash_hmac('sha256', json_encode($payload['data'] ?? $payload), $eventSecret);

        if (! $providedSignature || ! hash_equals($calculatedSignature, $providedSignature)) {
            return $this->failed($request);
        }

        $transactionData = $payload['data']['transaction'] ?? $payload;
        $status = $transactionData['status'] ?? null;
        $amount = isset($transactionData['amount_in_cents']) ? ($transactionData['amount_in_cents'] / 100) : 0;

        if ($status !== 'APPROVED') {
            return $this->failed($request);
        }

        try {
            DB::beginTransaction();

            $transaction = Transaction::create([
                'transaction_id' => $transactionData['id'] ?? '',
                'payment_type'   => Transaction::TYPE_WOMPI,
                'amount'         => $amount,
                'status'         => Subscription::ACTIVE,
                'user_id'        => $data['user_id'],
                'meta'           => json_encode($payload),
            ]);

            $planData['plan'] = $plan->toArray();
            $planData['user_id'] = $data['user_id'];
            $planData['payment_type'] = Subscription::TYPE_WOMPI;
            $planData['transaction_id'] = $transaction->id;

            $subscription = CreateSubscription::run($planData);

            DB::commit();

            if ($subscription) {
                setPlanFeatures();
                Notification::make()
                    ->success()
                    ->title(getLoggedInUser()->first_name . ' ' . __('messages.new_change.subscribed_success'))
                    ->send();

                return redirect(route('filament.hospitalAdmin.pages.subscription-plans'));
            }
        } catch (HttpException $ex) {
            DB::rollBack();
            throw $ex;
        }

        return redirect(route('filament.hospitalAdmin.pages.subscription-plans'));
    }

    public function failed(Request $request)
    {
        Notification::make()
            ->danger()
            ->title(__('messages.payment.payment_failed'))
            ->send();

        return redirect(route('filament.hospitalAdmin.pages.subscription-plans'));
    }
}
