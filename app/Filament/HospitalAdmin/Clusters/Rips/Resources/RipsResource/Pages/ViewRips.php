<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRips extends ViewRecord
{
    protected static string $resource = RipsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
