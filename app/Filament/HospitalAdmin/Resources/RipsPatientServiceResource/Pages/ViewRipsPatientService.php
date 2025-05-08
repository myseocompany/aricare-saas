<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Pages;

use App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRipsPatientService extends ViewRecord
{
    protected static string $resource = RipsPatientServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
