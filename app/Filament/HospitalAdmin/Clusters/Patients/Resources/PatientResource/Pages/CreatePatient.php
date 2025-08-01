<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use App\Repositories\PatientRepository;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;
use App\Models\Patient;

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
        // Asegura campos necesarios para crear el User
        $input['email'] = $input['email'] ?? 'paciente_' . uniqid() . '@aricare.co';
        $input['password'] = $input['password'] ?? 'PacienteTemp123!';
        $input['password_confirmation'] = $input['password_confirmation'] ?? 'PacienteTemp123!';
        $input['phone'] = $input['phone'] ?? '+573000000000';
        $input['region_code'] = $input['region_code'] ?? '+57';
        $input['designation'] = 'patient';
        $input['language'] = app()->getLocale();
        $input['status'] = true;
        $input['department_id'] = \App\Models\Department::where('name', 'Patient')->first()->id ?? 3;
        $input['theme_mode'] = '0';
    
        // fallback al tenant si no lo tiene (por si lo pierdes en tests)
        $input['tenant_id'] = $input['tenant_id'] ?? getLoggedInUser()?->tenant_id;
    
        // Intenta crear
        $record = app(PatientRepository::class)->store($input);
    
        if (! $record instanceof Model) {
            throw new \RuntimeException('Error creando el paciente');
        }
    
        app(PatientRepository::class)->createNotification($input);
        return $record;
    }
    
    

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.flash.Patient_saved');
    }

    protected function afterCreate(): void
    {
        /** @var \App\Models\Patient $patient */
        $patient = $this->record;

        $user = $patient->user;

        if ($user) {
            $user->owner_id = $patient->id;
            $user->owner_type = Patient::class;
            $user->save();
        }
    }
}
