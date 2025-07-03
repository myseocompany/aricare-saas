<?php

namespace App\Helpers;

use App\Models\Rips\RipsPatientService;

class RipsFormatter
{
    public static function formatForForm(RipsPatientService $record): array
    {
        $record->load([
            'consultations.diagnoses',
            'consultations.principalDiagnoses',
            'consultations.relatedDiagnoses',
            'procedures',
            'billingDocument',
        ]);

        $data = $record->toArray();

        // Factura
        if ($record->billingDocument) {
            $data['billing_document_id'] = $record->billing_document_id;
            $data['billing_document_number'] = $record->billingDocument->document_number;
            $data['agreement_id'] = $record->billingDocument->agreement_id;
            $data['billing_document_issued_at'] = $record->billingDocument->issued_at?->format('Y-m-d\TH:i');
        }

        // Consultas
        $data['consultations'] = $record->consultations->map(function ($consultation) {
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
                'principal_diagnoses' => $consultation->principalDiagnoses->map(fn ($d) => [
                    'cie10_id' => $d->cie10_id,
                    'rips_diagnosis_type_id' => $d->rips_diagnosis_type_id,
                ])->toArray(),
                'related_diagnoses' => $consultation->relatedDiagnoses->sortBy('sequence')->pluck('cie10_id')->toArray(),
            ];
        })->toArray();

        // Procedimientos
        $data['procedures'] = $record->procedures->map(function ($procedure) {
            return [
                'rips_admission_route_id' => $procedure->rips_admission_route_id,
                'rips_service_group_mode_id' => $procedure->rips_service_group_mode_id,
                'rips_service_group_id' => $procedure->rips_service_group_id,
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

        return $data;
    }
}
