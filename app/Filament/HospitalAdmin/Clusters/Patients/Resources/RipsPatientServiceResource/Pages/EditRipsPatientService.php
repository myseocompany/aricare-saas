<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRipsPatientService extends EditRecord
{
    protected static string $resource = RipsPatientServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // 🚨 Actualizar primero la factura asociada
        $billingDocument = $record->billingDocument;

        if ($billingDocument) {
            $billingDocument->update([
                'document_number' => $data['invoice_number'],
                'agreement_id' => $data['agreement_id'],
            ]);
        }

        unset($data['invoice_number']);
        unset($data['agreement_id']);

        // 🚨 Actualizar el registro principal
        $record->update($data);

        // 🚨 Limpiar consultas y diagnósticos existentes
        $record->consultations()->each(function ($consultation) {
            $consultation->diagnoses()->delete();
            $consultation->delete();
        });
        $record->procedures()->delete();

        // 🚀 Volver a crear las consultas y diagnósticos
        $consultations = $data['consultations'] ?? [];

        foreach ($consultations as $consultationData) {
            $consultation = $record->consultations()->create([
                'rips_cups_id' => $consultationData['rips_cups_id'],
                'rips_service_group_id' => $consultationData['rips_service_group_id'],
                'rips_service_group_mode_id' => $consultationData['rips_service_group_mode_id'] ?? null,
                'rips_service_reason_id' => $consultationData['rips_service_reason_id'] ?? null,
                'rips_consultation_cups_id' => $consultationData['rips_consultation_cups_id'] ?? null,
                'rips_service_id' => $consultationData['rips_service_id'],
                'rips_technology_purpose_id' => $consultationData['rips_technology_purpose_id'],
                'rips_collection_concept_id' => $consultationData['rips_collection_concept_id'],
                'copayment_receipt_number' => $consultationData['copayment_receipt_number'],
                'service_value' => $consultationData['service_value'],
                'copayment_value' => $consultationData['copayment_value'],
            ]);

            foreach ($consultationData['diagnoses'] ?? [] as $diagnosis) {
                $consultation->diagnoses()->create($diagnosis);
            }
        }

        foreach ($data['procedures'] ?? [] as $procedureData) {
            $record->procedures()->create([
                'rips_admission_route_id' => $procedureData['rips_admission_route_id'] ?? null,
                'rips_service_group_mode_id' => $procedureData['rips_service_group_mode_id'] ?? null,
                'rips_service_group_id' => $procedureData['rips_service_group_id'] ?? null,
                'rips_collection_concept_id' => $procedureData['rips_collection_concept_id'] ?? null,
                'mipres_id' => $procedureData['mipres_id'] ?? null,
                'authorization_number' => $procedureData['authorization_number'] ?? null,
                'rips_cups_id' => $procedureData['rips_cups_id'] ?? null,
                'cie10_id' => $procedureData['cie10_id'] ?? null,
                'surgery_cie10_id' => $procedureData['surgery_cie10_id'] ?? null,
                'rips_complication_cie10_id' => $procedureData['rips_complication_cie10_id'] ?? null,
                'service_value' => $procedureData['service_value'] ?? null,
                'copayment_value' => $procedureData['copayment_value'] ?? null,
                'copayment_receipt_number' => $procedureData['copayment_receipt_number'] ?? null,
            ]);
        }
        return $record;
    }

    // 🚨 Mutar antes de guardar - igual que en Create
    protected function mutateFormDataBeforeSave(array $data): array
    {
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
}
