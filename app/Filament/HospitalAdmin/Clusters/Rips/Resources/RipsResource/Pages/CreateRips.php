<?php

namespace App\Filament\HospitalAdmin\Clusters\RIPS\Resources\RipsResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource;
use App\Models\Rips\RipsBillingDocument;
use App\Services\RipsPatientServiceStatusUpdater;


use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Filament\Actions;

use App\Actions\Rips\CreateServiceTemplateFromService;
use App\Actions\Rips\LoadTemplateToForm;
use App\Actions\Rips\FormSyncConsultationsAndProcedures;
use Livewire\Attributes\On;

class CreateRips extends CreateRecord
{

    protected static string $resource = RipsResource::class;
    

    protected function handleRecordCreation(array $data): Model
    {
        Log::info("CreateRipsPatientService=>", $data);
        $tenantId = auth()->user()->tenant_id;
        $data['tenant_id'] = $tenantId;

        $billingDocument = null;
        if (!empty($data['billing_document_id'])) {
            $billingDocument = RipsBillingDocument::where('tenant_id', $tenantId)
                ->find($data['billing_document_id']);
            if ($billingDocument) {
                $billingDocument->update([
                    'issued_at' => $data['service_datetime'],
                ]);
            }
        }

        $record = static::getModel()::create($data);

        if ($billingDocument) {
            $record->billing_document_id = $billingDocument->id;
            $record->save();
        }
        // ✅ Llama aquí al servicio que actualiza el estado automáticamente
        app(RipsPatientServiceStatusUpdater::class)->actualizarEstado($record);
        app(FormSyncConsultationsAndProcedures::class)($record, $data);

        return $record;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
            // Combina fecha y hora en un solo campo datetime
        $data['service_datetime'] = $data['service_date'] . ' ' . $data['service_time'];

        // Puedes eliminar los campos separados si no se necesitan en la BD
        unset($data['service_date'], $data['service_time']);

        foreach ($data['consultations'] as &$consultation) {
            $diagnoses = [];
            //Log::info('CONSULTATION CON DIAGNOSES', $consultation['diagnoses']);
            if (!empty($consultation['principal_diagnoses'])) {
                foreach ($consultation['principal_diagnoses'] as $diagnosis) {
                    $diagnosis['sequence'] = 1;
                    $diagnoses[] = $diagnosis;
                }
            }

            if (!empty($consultation['related_diagnoses'])) {
                foreach ($consultation['related_diagnoses'] as $index => $cie10Id) {
                    $diagnoses[] = [
                        'cie10_id' => $cie10Id,
                        'rips_diagnosis_type_id' => null,
                        'sequence' => $index + 2,
                    ];
                }
            }

            $diagnosesData = $consultation['diagnoses'] ?? [];

            // Eliminamos estos campos temporales, no existen en la DB
            unset($consultation['principal_diagnoses']);
            unset($consultation['related_diagnoses']);
        }
        Log::info('FINAL FORM DATA', $data);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getState();

        if (!empty($data['save_as_template']) && !empty($data['template_name'])) {
            app(CreateServiceTemplateFromService::class)(
                $this->record,
                $data['template_name']
            );
        }
    }

   
}
