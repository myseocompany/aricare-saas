<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPatientServiceTemplate\Resources\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPatientServiceTemplate\Resources\RipsPatientServiceTemplateResource;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRipsPatientServiceTemplate extends EditRecord
{
    protected static string $resource = RipsPatientServiceTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
