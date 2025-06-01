<?php

namespace App\Filament\HospitalAdmin\Clusters\RipsPayers\Resources\Rips\RipsPayerResource\Pages;

use App\Filament\HospitalAdmin\Clusters\RipsPayers\Resources\Rips\RipsPayerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRipsPayer extends CreateRecord
{
    protected static string $resource = RipsPayerResource::class;
}
