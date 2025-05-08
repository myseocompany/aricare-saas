<?php

namespace App\Filament\Clusters\Settings\Resources\CupsResource\Pages;

use App\Filament\Clusters\Settings\Resources\CupsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCups extends EditRecord
{
    protected static string $resource = CupsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
