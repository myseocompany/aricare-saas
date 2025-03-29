<?php

namespace App\Filament\HospitalAdmin\Clusters\Radiology\Resources\RadiologyTestResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\HospitalAdmin\Clusters\Radiology\Resources\RadiologyTestResource;

class CreateRadiologyTest extends CreateRecord
{
    protected static string $resource = RadiologyTestResource::class;

    protected static bool $canCreateAnother = false;

    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    // protected function handleRecordCreation(array $input): Model
    // {
    //     dd($input);
    // }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.flash.radiology_test_saved');
    }
}
