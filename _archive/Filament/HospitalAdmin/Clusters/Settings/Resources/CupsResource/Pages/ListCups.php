<?php

namespace App\Filament\Clusters\Settings\Resources\CupsResource\Pages;

use App\Filament\Clusters\Settings\Resources\CupsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCups extends ListRecords
{
    protected static string $resource = CupsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
