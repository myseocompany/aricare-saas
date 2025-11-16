<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayer\RipsPayerResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayer\RipsPayerResource;
use Filament\Resources\Pages\EditRecord;

class EditRipsPayer extends EditRecord
{
    protected static string $resource = RipsPayerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
