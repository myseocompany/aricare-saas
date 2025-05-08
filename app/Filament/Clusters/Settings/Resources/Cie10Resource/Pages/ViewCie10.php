<?php

namespace App\Filament\Clusters\Settings\Resources\Cie10Resource\Pages;

use App\Filament\Clusters\Settings\Resources\Cie10Resource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCie10 extends ViewRecord
{
    protected static string $resource = Cie10Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
