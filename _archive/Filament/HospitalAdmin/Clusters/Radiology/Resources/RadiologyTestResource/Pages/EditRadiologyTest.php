<?php

namespace App\Filament\HospitalAdmin\Clusters\Radiology\Resources\RadiologyTestResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\HospitalAdmin\Clusters\Radiology\Resources\RadiologyTestResource;

class EditRadiologyTest extends EditRecord
{
    protected static string $resource = RadiologyTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! canAccessRecord($record, $record->id)) {
            Notification::make()
                ->danger()
                ->title(__('messages.flash.not_allow_access_record'))
                ->send();

            return $record;
        }
        return parent::handleRecordUpdate($record, $data);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.flash.radiology_test_updated');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
