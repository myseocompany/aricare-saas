<?php

namespace App\Filament\HospitalAdmin\Clusters\Users\Resources\LabTechnicianResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\HospitalAdmin\Clusters\Users\Resources\LabTechnicianResource;

class ViewLabTechnician extends ViewRecord
{
    protected static string $resource = LabTechnicianResource::class;

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
