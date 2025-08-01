<?php

namespace App\Filament\HospitalAdmin\Clusters\Users\Resources\ReceptionistResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\HospitalAdmin\Clusters\Users\Resources\ReceptionistResource;

class ViewReceptionist extends ViewRecord
{
    protected static string $resource = ReceptionistResource::class;

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
