<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Address;
use App\Models\Patient;
use Illuminate\Support\Arr;
use App\Repositories\PatientRepository;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;
// ⬇️ ADDED: importar la acción que corrige la tabla rips_statuses
use App\Actions\Rips\FixRipsStatuses; 

class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),

            // ⬇️ ADDED: botón temporal para corregir rips_statuses
            Actions\Action::make('fix-rips-statuses')
                ->label('Actualizar estados RIPS')
                ->icon('heroicon-o-wrench')
                ->color('warning')
                // Solo visible en local o para Admin
                ->visible(fn () => app()->environment('local') || auth()->user()->hasRole('Admin'))
                ->requiresConfirmation()
                ->modalHeading('Actualizar estados RIPS')
                ->modalDescription('Esto borrará e insertará los 5 estados estándar en la tabla rips_statuses (Incompleto, Listo, SinEnviar, Aceptado, Rechazado).')
                ->action(function () {
                    FixRipsStatuses::run();

                    Notification::make()
                        ->title('Estados RIPS actualizados')
                        ->success()
                        ->send();
                }),
            // ⬆️ END ADDED
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!canAccessRecord(Patient::class, $data['id'])) {
            Notification::make()
                ->danger()
                ->title(__('messages.flash.access_denied'))
                ->body(__('messages.flash.not_allow_access_record'))
                ->send();
            return $data;
        }

        $record = $this->record;
        $data = Patient::with(['user', 'address'])->where('id', $record->id)->get()->toArray();
        $data = $data[0] + $data[0]['user'] + ($data[0]['address'] ?? []) + ($data[0]['custom_field'] ?? []);
        $data = Arr::except($data, ['media', 'profile', 'user', 'address', 'custom_field', 'owner_type', 'owner_id', 'template_id']);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (array_key_exists('phone', $data) && !empty($data['phone'])) {
            $data['region_code'] = getRegionCode($data['region_code'] ?? '');
            $data['phone'] = getPhoneNumber($data['phone']);
        } else {
            $data['region_code'] = null;
        }

        $patient = app(PatientRepository::class)->update($record, $data);

        return $patient;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.flash.Patient_updated');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
