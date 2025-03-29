<?php

namespace App\Filament\HospitalAdmin\Clusters\Medicine\Resources\PurchaseMedicineResource\Pages;

use Filament\Actions\Action;
use App\Models\PurchaseMedicine;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Repositories\PurchaseMedicineRepository;
use App\Http\Controllers\PurchaseMedicineController;
use App\Filament\HospitalAdmin\Clusters\Medicine\Resources\PurchaseMedicineResource;
use Exception;

class CreatePurchaseMedicine extends CreateRecord
{
    protected static string $resource = PurchaseMedicineResource::class;

    protected static bool $canCreateAnother = false;

    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function handleRecordCreation(array $input): Model
    {
        try {
            $input['total'] = removeCommaFromNumbers(number_format($input['total'], 2));
            $input['discount'] = removeCommaFromNumbers(number_format($input['discount'], 2));
            $input['tax'] = removeCommaFromNumbers(number_format($input['tax'], 2));
            $input['net_amount'] = removeCommaFromNumbers(number_format($input['net_amount'], 2));

            $purchaseMedicineController = app(PurchaseMedicineController::class);
            $data = $purchaseMedicineController->store($input);

            if (is_array($data) && array_key_exists('error', $data)) {
                Notification::make()
                    ->title($data['error'])
                    ->danger()
                    ->send();
                $this->halt();
            }
            if (is_array($data) && array_key_exists('payment_mode', $data)) {
                $this->js('razorPay(event' . ',' . $data['status'] . ', ' . $data['record'] . ', ' . $data['amount'] . ')');
                $this->halt();
            }

            $medicinePurchase = [
                'purchase_no' => "1234",
            ];

            $purchaseMedicine = new PurchaseMedicine($medicinePurchase);
            return $purchaseMedicine;
        } catch (Exception $e) {
            Notification::make()->title($e->getMessage())->danger()->send();
            $this->halt();
        }
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
            return __('messages.new_change.medicine_purchase_success');
        }
        return '';
    }
}
