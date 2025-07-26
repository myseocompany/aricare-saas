<?php

namespace App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource\Pages;

use App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDoctor extends EditRecord
{
    protected static string $resource = DoctorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
