<?php

namespace App\Filament\HospitalAdmin\Clusters\RipsPayers\Resources\Rips\RipsPayerResource\Pages;

use App\Filament\HospitalAdmin\Clusters\RipsPayers\Resources\Rips\RipsPayerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRipsPayer extends EditRecord
{
    protected static string $resource = RipsPayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
