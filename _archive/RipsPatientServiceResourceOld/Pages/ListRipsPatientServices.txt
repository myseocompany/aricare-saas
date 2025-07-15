<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Pages;

use App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRipsPatientServices extends ListRecords
{
    protected static string $resource = RipsPatientServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
