<?php

namespace App\Filament\HospitalAdmin\Clusters\IpdOpd\Resources\OpdPatientResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Filament\HospitalAdmin\Clusters\IpdOpd\Resources\OpdPatientResource;

class EditOpdPatient extends EditRecord
{
    protected static string $resource = OpdPatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }
    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.flash.OPD_Patient_updated');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
