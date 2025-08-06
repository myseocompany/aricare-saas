<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPatientServiceTemplate\Resources\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPatientServiceTemplate\Resources\RipsPatientServiceTemplateResource;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRipsPatientServiceTemplates extends ListRecords
{
    protected static string $resource = RipsPatientServiceTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }
}
