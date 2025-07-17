<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\CaseHandlerResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Repositories\CaseHandlerRepository;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\CaseHandlerResource;

class CreateCaseHandler extends CreateRecord
{
    protected static string $resource = CaseHandlerResource::class;

    protected static bool $canCreateAnother = false;

    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function handleRecordCreation(array $input): Model
    {
        $input['region_code'] = !empty($input['phone']) ? getRegionCode($input['region_code'] ?? '') : null;
        $input['phone'] = getPhoneNumber($input['phone']);

        $record =  app(CaseHandlerRepository::class)->store($input);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.flash.case_saved');
    }
}
