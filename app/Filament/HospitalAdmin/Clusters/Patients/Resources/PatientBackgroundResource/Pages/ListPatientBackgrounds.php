<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientBackgroundResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientBackgroundResource\PatientBackgroundResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPatientBackgrounds extends ListRecords
{
    protected static string $resource = PatientBackgroundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
