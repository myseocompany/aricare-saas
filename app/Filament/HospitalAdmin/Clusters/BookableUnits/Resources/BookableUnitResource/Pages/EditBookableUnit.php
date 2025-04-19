<?php

namespace App\Filament\HospitalAdmin\Clusters\BookableUnits\Resources\BookableUnitResource\Pages;

use App\Filament\HospitalAdmin\Clusters\BookableUnits\Resources\BookableUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookableUnit extends EditRecord
{
    protected static string $resource = BookableUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
