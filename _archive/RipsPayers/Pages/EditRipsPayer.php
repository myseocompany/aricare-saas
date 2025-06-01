<?php


namespace App\Filament\HospitalAdmin\Clusters\RipsPayer\Pages;

use App\Filament\HospitalAdmin\Clusters\RipsPayer;
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
