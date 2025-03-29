<?php

namespace App\Filament\HospitalAdmin\Clusters\Medicine\Resources\UsedMedicineResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Medicine\Resources\UsedMedicineResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUsedMedicine extends CreateRecord
{
    protected static string $resource = UsedMedicineResource::class;
}
