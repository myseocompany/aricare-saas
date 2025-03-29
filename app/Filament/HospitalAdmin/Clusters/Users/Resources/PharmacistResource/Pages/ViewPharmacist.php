<?php

namespace App\Filament\HospitalAdmin\Clusters\Users\Resources\PharmacistResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\HospitalAdmin\Clusters\Users\Resources\PharmacistResource;

class ViewPharmacist extends ViewRecord
{
    protected static string $resource = PharmacistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
