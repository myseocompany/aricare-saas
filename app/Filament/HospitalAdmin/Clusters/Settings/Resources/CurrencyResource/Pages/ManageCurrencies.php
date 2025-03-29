<?php

namespace App\Filament\HospitalAdmin\Clusters\Settings\Resources\CurrencyResource\Pages;

use Filament\Actions;
use App\Models\CurrencySetting;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\HospitalAdmin\Clusters\Settings\Resources\CurrencyResource;

class ManageCurrencies extends ManageRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth("md")->createAnother(false)->successNotificationTitle(__('messages.new_change.currency_store'))
            ->action(function (array $data) {
                $data = [
                    'currency_name' => $data['currency_name'],
                    'currency_code' => strtoupper($data['currency_code']),
                    'currency_icon' => $data['currency_icon'],
                ];

                CurrencySetting::create($data);
            }),
        ];
    }
}
