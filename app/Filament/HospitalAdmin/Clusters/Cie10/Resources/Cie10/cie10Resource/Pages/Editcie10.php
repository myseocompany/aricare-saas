<?php

namespace App\Filament\HospitalAdmin\Clusters\cie10\Resources\Cie10\cie10Resource\Pages;

use App\Filament\HospitalAdmin\Clusters\cie10\Resources\Cie10\cie10Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class Editcie10 extends EditRecord
{
    protected static string $resource = cie10Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
