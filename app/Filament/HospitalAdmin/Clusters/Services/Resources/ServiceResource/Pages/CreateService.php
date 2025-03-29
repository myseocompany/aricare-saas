<?php

namespace App\Filament\HospitalAdmin\Clusters\Services\Resources\ServiceResource\Pages;

use Filament\Actions;
use App\Repositories\ServiceRepository;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\HospitalAdmin\Clusters\Services\Resources\ServiceResource;
use Filament\Actions\Action;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.flash.service_saved');
    }

    protected function handleRecordCreation(array $input): Model
    {
        app(ServiceRepository::class)->createNotification();

        return parent::handleRecordCreation($input);
    }

    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
