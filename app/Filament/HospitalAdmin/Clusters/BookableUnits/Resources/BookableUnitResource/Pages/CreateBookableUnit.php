<?php

namespace App\Filament\HospitalAdmin\Clusters\BookableUnits\Resources\BookableUnitResource\Pages;

use App\Filament\HospitalAdmin\Clusters\BookableUnits\Resources\BookableUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBookableUnit extends CreateRecord
{
    protected static string $resource = BookableUnitResource::class;


    
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

}
