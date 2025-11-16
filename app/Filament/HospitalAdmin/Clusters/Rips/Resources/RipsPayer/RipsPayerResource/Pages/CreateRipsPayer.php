<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayer\RipsPayerResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayer\RipsPayerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRipsPayer extends CreateRecord
{
    protected static string $resource = RipsPayerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = auth()->user()->tenant_id;

        return $data;
    }
}
