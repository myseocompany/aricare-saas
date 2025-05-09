<?php

namespace App\Filament\HospitalAdmin\Clusters\cie10\Resources\Cie10\cie10Resource\Pages;

use App\Filament\HospitalAdmin\Clusters\cie10\Resources\Cie10\cie10Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class Listcie10s extends ListRecords
{
    protected static string $resource = cie10Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
