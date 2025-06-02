<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Rips\RipsBillingDocument;
use App\Models\Rips\RipsTenantPayerAgreement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class CreateRipsPatientService extends CreateRecord
{
    protected static string $resource = RipsPatientServiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        Log::info("CreateRipsPatientService=>", $data);
        $tenantId = auth()->user()->tenant_id;

        // 🚨 Validaciones
        if (empty($data['invoice_number'])) {
            throw ValidationException::withMessages([
                'invoice_number' => 'El número de factura es requerido.',
            ]);
        }

        if (empty($data['agreement_id'])) {
            throw ValidationException::withMessages([
                'agreement_id' => 'El convenio/contrato es requerido.',
            ]);
        }

        // Verificar que no exista el número de factura en este tenant
        if (RipsBillingDocument::where('tenant_id', $tenantId)
            ->where('document_number', $data['invoice_number'])
            ->exists()) {
            throw ValidationException::withMessages([
                'invoice_number' => 'El número de factura ya existe en este tenant.',
            ]);
        }

        // Verificar que el acuerdo exista
        if (!RipsTenantPayerAgreement::where('id', $data['agreement_id'])->exists()) {
            throw ValidationException::withMessages([
                'agreement_id' => 'El convenio/contrato seleccionado no es válido.',
            ]);
        }

        // 🚀 Crear la factura
        $billingDocument = RipsBillingDocument::create([
            'tenant_id' => $tenantId,
            'type_id' => 1, // Factura
            'document_number' => $data['invoice_number'],
            'issued_at' => $data['service_datetime'],
            'agreement_id' => $data['agreement_id'],
            'total_amount' => 0,
            'net_amount' => 0,
        ]);

        // Asignar factura al servicio
        $data['billing_document_id'] = $billingDocument->id;

        // 🚨 Opcional: Limpiar campos si no quieres que se guarden
        unset($data['invoice_number']);
        unset($data['agreement_id']);

        $record = static::getModel()::create($data);

        // Actualizar el `billing_document_id` manualmente
        $record->billing_document_id = $billingDocument->id;
        $record->save();
        
        // 🚨 Capturar las consultas
    $consultations = $data['consultations'] ?? [];

    //dd($consultations);

    foreach ($consultations as $consultationData) {
        // Primero crea la consulta
        $consultation = $record->consultations()->create([
            'rips_cups_id' => $consultationData['rips_cups_id'],
            'rips_service_group_id' => $consultationData['rips_service_group_id'],
            'rips_service_id' => $consultationData['rips_service_id'],
            'rips_technology_purpose_id' => $consultationData['rips_technology_purpose_id'],
            'rips_collection_concept_id' => $consultationData['rips_collection_concept_id'],
            'copayment_receipt_number' => $consultationData['copayment_receipt_number'],
            'service_value' => $consultationData['service_value'],
            'copayment_value' => $consultationData['copayment_value'],
            // Puedes agregar más campos si los tienes
        ]);

        $diagnoses = array_merge(
            collect($consultationData['principal_diagnoses'] ?? [])->map(function ($item) {
                $item['sequence'] = 1; // Principal es siempre 1
                return $item;
            })->toArray(),
            collect($consultationData['related_diagnoses'] ?? [])->map(function ($item, $key) {
                $item['sequence'] = $key + 2; // 2, 3, 4...
                $item['rips_diagnosis_type_id'] = null; // Relacionados no tienen tipo
                return $item;
            })->toArray()
        );

        
        foreach ($diagnoses as $diagnosis) {
            $consultation->diagnoses()->create([
                'cie10_id' => $diagnosis['cie10_id'],
                'rips_diagnosis_type_id' => $diagnosis['rips_diagnosis_type_id'] ?? null,
                'sequence' => $diagnosis['sequence'],
            ]);
        }

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
