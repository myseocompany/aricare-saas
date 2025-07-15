<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Pages;

use App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRipsPatientService extends EditRecord
{
    protected static string $resource = RipsPatientServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
