<?php

namespace App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource\Pages;

use App\Models\User;
use App\Models\Doctor;
use Filament\Actions;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource;

class EditDoctor extends EditRecord
{
    protected static string $resource = DoctorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record;
        $data = Doctor::with(['user'])
            ->where('id', $record->id)
            ->get()
            ->toArray();

        // Merge doctor + user
        $data = $data[0] + $data[0]['user'];

        // Nos aseguramos que sea integer
        $data['rips_identification_type_id'] = $data['rips_identification_type_id'] ?? null;

        $data = Arr::except($data, ['media', 'profile', 'user', 'owner_type', 'owner_id']);

        return $data;
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Actualizar User
        $record->user->update([
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'rips_identification_type_id' => $data['rips_identification_type_id'] ?? null,
            'rips_identification_number' => $data['rips_identification_number'] ?? null,
        ]);

        // Remover datos que son del User para evitar error en Doctor
        unset(
            $data['first_name'],
            $data['last_name'],
            $data['rips_identification_type_id'],
            $data['rips_identification_number']
        );

        // Actualizar Doctor
        $record->update($data);

        return $record;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.flash.doctor_update');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
