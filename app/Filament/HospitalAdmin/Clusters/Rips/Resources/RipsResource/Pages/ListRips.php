<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRips extends ListRecords
{
    protected static string $resource = RipsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
       public static function getWidgets(): array
    {
        return [
            StatusOverview::class,  // Registramos el widget StatusOverview
        ];
    }

}
