<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRips extends EditRecord
{
    protected static string $resource = RipsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
