<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsTenantPayerAgreement\RipsTenantPayerAgreementResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsTenantPayerAgreement\RipsTenantPayerAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRipsTenantPayerAgreements extends ListRecords
{
    protected static string $resource = RipsTenantPayerAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
