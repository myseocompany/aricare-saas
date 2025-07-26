<?php

namespace App\Actions\Rips;

use App\Models\Rips\RipsPatientService;

class SyncConsultationsAndProcedures
{
    
    
    public function __invoke(RipsPatientService $record, array $data): void
    {
        // Borrar lo anterior :)
        $record->consultations()->each(function ($consultation) {
            $consultation->diagnoses()->delete();
            $consultation->delete();
        });
        $record->procedures()->delete();
        
        

        // Guardar consultas
        foreach ($data['consultations'] ?? [] as $consultationData) {
            $consultation = $record->consultations()->create([
                'rips_cups_id' => $consultationData['rips_cups_id'],
                'rips_service_group_id' => $consultationData['rips_service_group_id'],
                'rips_service_group_mode_id' => $consultationData['rips_service_group_mode_id'] ?? null,
                'rips_service_reason_id' => $consultationData['rips_service_reason_id'] ?? null,
                'rips_consultation_cups_id' => $consultationData['rips_consultation_cups_id'] ?? null,
                'rips_service_id' => $consultationData['rips_service_id'],
                'rips_technology_purpose_id' => $consultationData['rips_technology_purpose_id'],
                'rips_collection_concept_id' => $consultationData['rips_collection_concept_id'],
                'copayment_receipt_number' => $consultationData['copayment_receipt_number'] ?? null,
                'service_value' => $consultationData['service_value'],
                'copayment_value' => $consultationData['copayment_value'],
            ]);


            foreach ($consultationData['diagnoses'] ?? [] as $diagnosis) {
                $consultation->diagnoses()->create([
                    'cie10_id' => $diagnosis['cie10_id'],
                    'rips_diagnosis_type_id' => $diagnosis['rips_diagnosis_type_id'] ?? null,
                    'sequence' => $diagnosis['sequence'],
                ]);
            }
        }
        // Guardar procedimientos
        foreach ($data['procedures'] ?? [] as $procedureData) {
            $record->procedures()->create([
                'rips_admission_route_id' => $procedureData['rips_admission_route_id'] ?? null,
                'rips_service_group_mode_id' => $procedureData['rips_service_group_mode_id'] ?? null,
                'rips_service_group_id' => $procedureData['rips_service_group_id'] ?? null,
                'rips_service_id' => isset($procedureData['rips_service_id']) ? (int) $procedureData['rips_service_id'] : null,

                'rips_collection_concept_id' => $procedureData['rips_collection_concept_id'] ?? null,
                'rips_technology_purpose_id' => $procedureData['rips_technology_purpose_id'] ?? null,
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
    }
}