<?php

namespace App\Filament\Clusters\Settings\Resources\Cie10Resource\Pages;

use App\Filament\Clusters\Settings\Resources\Cie10Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCie10s extends ListRecords
{
    protected static string $resource = Cie10Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
