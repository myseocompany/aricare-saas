<?php

namespace App\Filament\Resources\RipsReportingCenterResource\Pages;

use App\Filament\Resources\RipsReportingCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRipsReportingCenter extends ViewRecord
{
    protected static string $resource = RipsReportingCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
