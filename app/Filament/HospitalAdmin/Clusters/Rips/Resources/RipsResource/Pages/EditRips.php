<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use App\Services\RipsPatientServiceStatusUpdater;


use App\Actions\Rips\CreateServiceTemplateFromService;
use App\Actions\Rips\LoadTemplateToForm;
use App\Actions\Rips\FormSyncConsultationsAndProcedures;




class EditRips extends EditRecord
{
    protected static string $resource = RipsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {


        // 🚨 Actualizar primero la factura asociada
        $billingDocument = null;
        if (!empty($data['billing_document_id'])) {
            $billingDocument = \App\Models\Rips\RipsBillingDocument::where('tenant_id', auth()->user()->tenant_id)
                ->find($data['billing_document_id']);
            if ($billingDocument) {
                $billingDocument->update([
                    'issued_at' => $data['service_datetime'],
                ]);
            }
        }

        // 🚨 Actualizar el registro principal
        $record->update($data);

        if ($billingDocument) {
            $record->billing_document_id = $billingDocument->id;
            $record->save();
        } else {
            $record->billing_document_id = null;
            $record->save();
        }
        // ✅ Sincroniza consultas y procedimientos
        app(FormSyncConsultationsAndProcedures::class)($record, $data);

        // ✅ Actualiza automáticamente el estado del servicio
        app(RipsPatientServiceStatusUpdater::class)->actualizarEstado($record);

        return $record;
    }

    // 🚨 Mutar antes de guardar - igual que en Create
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Combina fecha y hora en un solo campo datetime
        $data['service_datetime'] = $data['service_date'] . ' ' . $data['service_time'];

        // Puedes eliminar los campos separados si no se necesitan en la BD
        unset($data['service_date'], $data['service_time']);
        foreach ($data['consultations'] as &$consultation) {
            $diagnoses = [];

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

            $consultation['diagnoses'] = $diagnoses;

            // Eliminamos estos campos temporales
            unset($consultation['principal_diagnoses']);
            unset($consultation['related_diagnoses']);
        }

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return app(\App\Actions\Rips\MapServiceDataForForm::class)($this->record);
    }

    protected function afterSave(): void
    {
        $data = $this->form->getState();


        if (!empty($data['save_as_template']) && !empty($data['template_name'])) {
            app(CreateServiceTemplateFromService::class)(
                $this->record, // RipsPatientService
                $data['template_name']
            );
        }
    }

    public function loadTemplate($templateId)
    {
        $data = app(LoadTemplateToForm::class)($templateId);
        if ($data) {
            $this->form->fill($data);
            $this->dispatch('refreshForm');
        }
    }

}
