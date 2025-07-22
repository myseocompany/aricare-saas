<?php

namespace App\Filament\HospitalAdmin\Clusters\TemplatesCluster\Resources\RipsPatientServiceTemplateResource\Pages;

use App\Filament\HospitalAdmin\Clusters\TemplatesCluster\Resources\RipsPatientServiceTemplateResource;
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
