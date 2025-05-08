<?php

namespace App\Filament\Clusters\Settings\Resources\Cie10Resource\Pages;

use App\Filament\Clusters\Settings\Resources\Cie10Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCie10 extends EditRecord
{
    protected static string $resource = Cie10Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
