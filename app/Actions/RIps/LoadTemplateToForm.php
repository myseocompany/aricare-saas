<?php

namespace App\Actions\Rips;

use App\Models\Rips\RipsPatientServiceTemplate;

class LoadTemplateToForm
{
    public function __invoke(int $templateId): array
    {
        $template = RipsPatientServiceTemplate::with([
            'consultations.diagnoses',
            'procedures',
        ])->find($templateId);

        if (!$template) return [];

        $consultations = $template->consultations->map(function ($consultation) {
            return [
                'rips_cups_id' => $consultation->rips_cups_id,
                'rips_service_group_id' => $consultation->rips_service_group_id,
                'rips_service_group_mode_id' => $consultation->rips_service_group_mode_id,
                'rips_service_reason_id' => $consultation->rips_service_reason_id,
                'rips_consultation_cups_id' => $consultation->rips_consultation_cups_id,
                'rips_service_id' => $consultation->rips_service_id,
                'rips_technology_purpose_id' => $consultation->rips_technology_purpose_id,
                'rips_collection_concept_id' => $consultation->rips_collection_concept_id,
                'copayment_receipt_number' => $consultation->copayment_receipt_number,
                'service_value' => $consultation->service_value,
                'copayment_value' => $consultation->copayment_value,
                'principal_diagnoses' => $consultation->diagnoses
                    ->where('sequence', 1)
                    ->map(fn ($d) => [
                        'cie10_id' => $d->cie10_id,
                        'rips_diagnosis_type_id' => $d->rips_diagnosis_type_id,
                    ])->values()->toArray(),
                'related_diagnoses' => $consultation->diagnoses
                    ->where('sequence', '>', 1)
                    ->map(fn ($d) => ['cie10_id' => $d->cie10_id]) // Aquí está el cambio
                    ->values()
                    ->toArray(),
            ];
        })->toArray();

        $procedures = $template->procedures->map(function ($procedure) {
            return [
                'rips_admission_route_id' => $procedure->rips_admission_route_id,
                'rips_service_group_mode_id' => $procedure->rips_service_group_mode_id,
                'rips_service_group_id' => $procedure->rips_service_group_id,
                'rips_service_id' => $procedure->rips_service_id,
                'rips_technology_purpose_id' => $procedure->rips_technology_purpose_id,
                'rips_collection_concept_id' => $procedure->rips_collection_concept_id,
                'mipres_id' => $procedure->mipres_id,
                'authorization_number' => $procedure->authorization_number,
                'rips_cups_id' => $procedure->rips_cups_id,
                'cie10_id' => $procedure->cie10_id,
                'surgery_cie10_id' => $procedure->surgery_cie10_id,
                'rips_complication_cie10_id' => $procedure->rips_complication_cie10_id,
                'copayment_receipt_number' => $procedure->copayment_receipt_number,
                'service_value' => $procedure->service_value,
                'copayment_value' => $procedure->copayment_value,
            ];
        })->toArray();

        return [
            'consultations' => $consultations,
            'procedures' => $procedures,
        ];
    }
}
