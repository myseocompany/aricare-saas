<?php

namespace App\Filament\HospitalAdmin\Clusters\BedManagement\Resources\BedAssignResource\Pages;

use Filament\Actions\Action;
use App\Repositories\BedAssignRepository;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\HospitalAdmin\Clusters\BedManagement\Resources\BedAssignResource;

class CreateBedAssign extends CreateRecord
{
    protected static string $resource = BedAssignResource::class;
    protected static bool $canCreateAnother = false;
    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }
    protected function afterCreate(): void
    {
        app(BedAssignRepository::class)->createNotification($this->record->toArray());
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.common.bed_assigned_successfully');
    }
}
