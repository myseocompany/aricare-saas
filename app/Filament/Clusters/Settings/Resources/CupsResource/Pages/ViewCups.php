<?php

namespace App\Filament\Clusters\Settings\Resources\CupsResource\Pages;

use App\Filament\Clusters\Settings\Resources\CupsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCups extends ViewRecord
{
    protected static string $resource = CupsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
