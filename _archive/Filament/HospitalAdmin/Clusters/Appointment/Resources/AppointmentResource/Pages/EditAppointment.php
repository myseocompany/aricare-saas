<?php

namespace App\Filament\HospitalAdmin\Clusters\Appointment\Resources\AppointmentResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Appointment\Resources\AppointmentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

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
        $data['opd_date'] = $data['opd_date'] . ' ' . $data['time'];
        
        return parent::handleRecordUpdate($record, $data);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.flash.appointment_updated');
    }
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
