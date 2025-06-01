<?php

namespace App\Filament\Resources\RipsReportingCenterResource\Pages;

use App\Filament\Resources\RipsReportingCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRipsReportingCenters extends ListRecords
{
    protected static string $resource = RipsReportingCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
