<?php

namespace App\Filament\HospitalAdmin\Clusters\Encounters\Resources\EncounterResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Encounters\Resources\EncounterResource\EncounterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEncounter extends EditRecord
{
    protected static string $resource = EncounterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
