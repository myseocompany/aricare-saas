<?php

namespace App\Filament\HospitalAdmin\Clusters\Encounters\Resources\EncounterResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Encounters\Resources\EncounterResource\EncounterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEncounter extends CreateRecord
{
    protected static string $resource = EncounterResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
