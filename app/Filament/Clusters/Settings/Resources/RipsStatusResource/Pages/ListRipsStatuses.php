<?php

namespace App\Filament\Clusters\Settings\Resources\RipsStatusResource\Pages;

use App\Filament\Clusters\Settings\Resources\RipsStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRipsStatuses extends ListRecords
{
    protected static string $resource = RipsStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
