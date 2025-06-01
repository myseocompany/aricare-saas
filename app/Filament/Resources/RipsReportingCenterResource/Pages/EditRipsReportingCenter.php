<?php

namespace App\Filament\Resources\RipsReportingCenterResource\Pages;

use App\Filament\Resources\RipsReportingCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRipsReportingCenter extends EditRecord
{
    protected static string $resource = RipsReportingCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
