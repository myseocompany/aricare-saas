<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource\Pages;


use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource\RipsPayerResource;


use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRipsPayer extends CreateRecord
{
    protected static string $resource = RipsPayerResource::class;
}
