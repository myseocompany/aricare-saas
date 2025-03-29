<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use App\Repositories\PatientRepository;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

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

    protected function handleRecordCreation(array $input): Model
    {
        $input['region_code'] = !empty($input['phone']) ? getRegionCode($input['region_code'] ?? '') : null;
        $input['phone'] = getPhoneNumber($input['phone']);

        $record =app(PatientRepository::class)->store($input);
        app(PatientRepository::class)->createNotification($input);
        return $record;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.flash.Patient_saved');
    }
}
