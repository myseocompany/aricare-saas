<?php

namespace App\Filament\HospitalAdmin\Clusters\IpdOpd\Resources\OpdPatientResource\Pages;

use App\Models\CustomField;
use Illuminate\Support\Arr;
use Filament\Actions\Action;
use App\Models\OpdPatientDepartment;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Repositories\OpdPatientDepartmentRepository;
use App\Filament\HospitalAdmin\Clusters\IpdOpd\Resources\OpdPatientResource;

class CreateOpdPatient extends CreateRecord
{
    public function mount(): void
    {
        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();

        $_GET['revisit'] ?? null;
    }

    protected static string $resource = OpdPatientResource::class;

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

    protected function fillForm(): void
    {
        if (isset($_GET['revisit']) && !empty($_GET['revisit'])) {
            $record = OpdPatientDepartment::where('id', $_GET['revisit'])->first()->toArray();
            $record = Arr::except($record, ['payment_mode', 'standard_charge', 'symptoms', 'notes', 'custom_field', 'is_old_patient', 'opd_number']);
            $record['opd_number'] = OpdPatientDepartment::generateUniqueOpdNumber();

            $this->form->fill($record);
        } else {
            $this->form->fill();
        }
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.flash.OPD_Patient_saved');
    }

    protected function handleRecordCreation(array $input): Model
    {
        $input['standard_charge'] = removeCommaFromNumbers($input['standard_charge']);
        $opdPatientDepartmentRepository = app(OpdPatientDepartmentRepository::class);
        $data = $opdPatientDepartmentRepository->store($input);
        $opdPatientDepartmentRepository->createNotification($input);
        return $data;
    }
}
