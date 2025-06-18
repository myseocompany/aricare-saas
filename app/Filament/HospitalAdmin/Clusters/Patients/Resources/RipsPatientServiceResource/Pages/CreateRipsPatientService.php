<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Rips\RipsBillingDocument;
use App\Models\Rips\RipsTenantPayerAgreement;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateRipsPatientService extends CreateRecord
{
    protected static string $resource = RipsPatientServiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        Log::info("CreateRipsPatientService=>", $data);
        $tenantId = auth()->user()->tenant_id;

        $xmlTempPath = $data['xml_file'] ?? null;
        unset($data['xml_file']);

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

            if ($xmlTempPath) {
                $agreement = RipsTenantPayerAgreement::find($billingDocument->agreement_id);
                $patientUserId = Patient::find($data['patient_id'])?->user_id;
                $directory = $tenantId . '/' . ($agreement->code ?? 'no-agreement') . '/' . $patientUserId;
                $filename = basename($xmlTempPath);
                Storage::disk('public')->makeDirectory($directory);
                Storage::disk('public')->move($xmlTempPath, $directory . '/' . $filename);
                $billingDocument->update([
                    'xml_path' => Storage::disk('public')->path($directory . '/' . $filename),
                ]);
            }
        }
        
        // 🚨 Capturar las consultas
    
    
        $consultations = $data['consultations'] ?? [];

        foreach ($consultations as $consultationData) {
            // Primero crea la consulta
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
            $consultation->diagnoses()->create([
                'cie10_id' => $diagnosis['cie10_id'],
                'rips_diagnosis_type_id' => $diagnosis['rips_diagnosis_type_id'] ?? null,
                'sequence' => $diagnosis['sequence'],
            ]);
        }

    } 

            // 🚨 Capturar los procedimientos
        foreach ($data['procedures'] ?? [] as $procedureData) {
            $record->procedures()->create([
                'rips_admission_route_id' => $procedureData['rips_admission_route_id'] ?? null,
                'rips_service_group_mode_id' => $procedureData['rips_service_group_mode_id'] ?? null,
                'rips_service_group_id' => $procedureData['rips_service_group_id'] ?? null,
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
        return $record;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
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

        // Eliminamos estos campos temporales, no existen en la DB
        unset($consultation['principal_diagnoses']);
        unset($consultation['related_diagnoses']);
    }

    return $data;
}


}
