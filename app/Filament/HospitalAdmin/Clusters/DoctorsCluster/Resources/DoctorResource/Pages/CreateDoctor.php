<?php

namespace App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource\Pages;

use App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;
}
