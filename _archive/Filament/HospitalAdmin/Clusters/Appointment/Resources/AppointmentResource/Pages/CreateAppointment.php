<?php

namespace App\Filament\HospitalAdmin\Clusters\Appointment\Resources\AppointmentResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Appointment\Resources\AppointmentResource;
use App\Mail\NotifyMailHospitalAdminForBookingAppointment;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use App\Models\UserTenant;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\AppointmentRepository;
use App\Repositories\AppointmentTransactionRepository;
use App\Http\Controllers\AppointmentTransactionController;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Stripe\ExchangeRate;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    protected static bool $canCreateAnother = false;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),

        ];
    }

    protected function handleRecordCreation(array $input): Model
    {
        if (!getLoggedInUser()->hasRole('Patient') && isset($input['appointment_charge'])) {
            $input['appointment_charge'] = removeCommaFromNumbers(number_format($input['appointment_charge'], 2));
        }

        $appointmentTransactionRepository = app(AppointmentTransactionRepository::class);

        $input['opd_date'] = $input['opd_date'] . ' ' . $input['time'];
        $input['payment_type'] = $input['payment_type'] ?? 4;
        $appointmentRepository = app(AppointmentRepository::class);

        $input['is_completed'] = $input['is_completed'] == 1 ? Appointment::STATUS_COMPLETED : Appointment::STATUS_PENDING;

        $input['payment_type'] = $input['payment_type'] ?? 4;
        if (auth()->user()->hasRole('Patient')) {
            $input['patient_id'] = auth()->user()->owner_id;
        }

        $jsonFields = [];

        foreach ($input as $key => $value) {
            if (strpos($key, 'field') === 0) {
                $jsonFields[$key] = $value;
            }
        }
        $input['custom_field'] = !empty($jsonFields) ? $jsonFields : null;

        if ($input['payment_type'] != 8 && $input['payment_type'] != 7) {
            $data = $appointmentRepository->create($input);
            $appointment = Appointment::find($data['id']);
            $input['appointment_id'] = $data['id'];
        }

        $appointmentRepository->createNotification($input);
        if (Auth::check()) {
            $hospitalDefaultAdmin = User::find(Auth::user()->id);
        }

        if (in_array($input['payment_type'], [1, 2, 3, 5])) {
            $data->update(['payment_type' => 4]);
        }

        if ($input['payment_type'] == 1) {
            $appointmentTransactionController = new AppointmentTransactionController($appointmentTransactionRepository);
            if (getCurrentCurrency() == "ngn" && $input['appointment_charge'] < 570) {
                $appointment = Appointment::find($input['appointment_id']);
                $appointment->delete();
                Notification::make()
                    ->danger()
                    ->title(__('messages.flash.appointment_charge_must_be_greater_than_570'))
                    ->send();
                Appointment::find($input['appointment_id'])->delete();
                session(['paymentError' => 'error']);
                return $appointment;
            }
            $data = $appointmentTransactionController->createStripeSession($input);
        } elseif ($input['payment_type'] == 2) {

            $data = app(AppointmentTransactionController::class)->appointmentRazorpayPayment($input);
            if (session()->has('appointmentPayment')) {
                $dataResponse = session()->get('appointmentPayment');
                session()->forget('appointmentPayment');
                $this->js('razorPay(event' . ',' . $dataResponse['status'] . ', ' . $dataResponse['record'] . ', ' . $dataResponse['amount'] . ')');
                $this->halt();
            }
        } elseif ($input['payment_type'] == 3) {
            if (! in_array(strtoupper(getCurrentCurrency()), getPayPalSupportedCurrencies())) {
                Appointment::whereId($input['appointment_id'])->delete();
                Notification::make()
                    ->title(__('messages.flash.currency_not_supported_paypal'))
                    ->danger()
                    ->send();
                $this->halt();
            }
            $appointmentTransactionController = new AppointmentTransactionController($appointmentTransactionRepository);
            $url = $appointmentTransactionController->paypalOnBoard($input);
        } elseif ($input['payment_type'] == 5) {
            if (!in_array(strtoupper(getCurrentCurrency()), flutterWaveSupportedCurrencies())) {
                Appointment::find($input['appointment_id'])->delete();
                Notification::make()
                    ->title(__('messages.common.something_want_wrong') . '!')
                    ->body(__('messages.flutterwave.currency_allowed'))
                    ->danger()
                    ->send();
                $this->halt();
            }
            $appointmentTransactionController = new AppointmentTransactionController($appointmentTransactionRepository);
            $data = $appointmentTransactionController->appointmentFlutterWavePayment($input);
        } elseif ($input['payment_type'] == 7) {
            $appointmentTransactionController = new AppointmentTransactionController($appointmentTransactionRepository);
            $data = $appointmentTransactionController->phonePayInit($input);
        } elseif ($input['payment_type'] == 8) {
            if (!in_array(strtoupper(getCurrentCurrency()), payStackSupportedCurrencies())) {
                Appointment::find($input['appointment_id'])->delete();
                Notification::make()
                    ->title(__('messages.new_change.paystack_support_zar'))
                    ->danger()
                    ->send();
                $this->halt();
            }
            $appointmentTransactionController = new AppointmentTransactionController($appointmentTransactionRepository);
            $data = $appointmentTransactionController->appointmentPaystackPayment($input);
        } else {
            $data = $appointmentTransactionRepository->store($data);
        }

        $userId = UserTenant::whereTenantId(getLoggedInUser()->tenant_id)->value('user_id');
        $hospitalDefaultAdmin = User::whereId($userId)->first();

        if (! empty($hospitalDefaultAdmin)) {

            $hospitalDefaultAdminEmail = $hospitalDefaultAdmin->email;
            $doctor = Doctor::whereId($input['doctor_id'])->first();
            $patient = Patient::whereId($input['patient_id'])->first();

            $mailData = [
                'booking_date' => Carbon::parse($input['opd_date'])->translatedFormat('g:i A') . ' ' . Carbon::parse($input['opd_date'])->translatedFormat('jS M, Y'),
                'patient_name' => $patient->user->full_name,
                'patient_email' => $patient->user->email,
                'doctor_name' => $doctor->user->full_name,
                'doctor_department' => $doctor->department->title,
                'doctor_email' => $doctor->user->email,
            ];

            $mailData['patient_type'] = 'Old';

            Mail::to($hospitalDefaultAdminEmail)
                ->send(new NotifyMailHospitalAdminForBookingAppointment(
                    'emails.booking_appointment_mail',
                    __('messages.new_change.notify_mail_for_patient_book'),
                    $mailData
                ));
            Mail::to($doctor->user->email)
                ->send(new NotifyMailHospitalAdminForBookingAppointment(
                    'emails.booking_appointment_mail',
                    __('messages.new_change.notify_mail_for_patient_book'),
                    $mailData
                ));
        }
        $data = [
            'patient_id' => 1,
            'doctor_id' => 2,
            'appointment_date' => '2025-01-25',
            'appointment_time' => '14:00:00',
            'notes' => 'Follow-up checkup',
        ];

        $appointment = new Appointment($data);

        return $appointment;
    }


    protected function getRedirectUrl(): string
    {
        if (session()->has('sessionUrl')) {
            $sessionUrl = session()->get('sessionUrl');
            session()->forget('sessionUrl');
            return $sessionUrl;
        } else {
            return static::getResource()::getUrl('index');
        }
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        if (session()->has('paymentError')) {
            session()->forget('paymentError');
            return '';
        } else if (! session()->has('sessionUrl')) {
            return __('messages.flash.appointment_created');
        }
        return '';
    }
}
