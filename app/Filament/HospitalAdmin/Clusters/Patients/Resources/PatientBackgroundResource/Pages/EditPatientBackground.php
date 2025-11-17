<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientBackgroundResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientBackgroundResource\PatientBackgroundResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPatientBackground extends EditRecord
{
    protected static string $resource = PatientBackgroundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
