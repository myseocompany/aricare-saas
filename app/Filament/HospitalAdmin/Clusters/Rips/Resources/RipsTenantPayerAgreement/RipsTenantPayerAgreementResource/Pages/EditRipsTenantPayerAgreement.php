<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsTenantPayerAgreement\RipsTenantPayerAgreementResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsTenantPayerAgreement\RipsTenantPayerAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRipsTenantPayerAgreement extends EditRecord
{
    protected static string $resource = RipsTenantPayerAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
