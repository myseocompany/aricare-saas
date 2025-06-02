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

        // 🚀 Volver a crear las consultas y diagnósticos
        $consultations = $data['consultations'] ?? [];

        foreach ($consultations as $consultationData) {
            $consultation = $record->consultations()->create([
                'rips_cups_id' => $consultationData['rips_cups_id'],
                'rips_service_group_id' => $consultationData['rips_service_group_id'],
                'rips_service_id' => $consultationData['rips_service_id'],
                'rips_technology_purpose_id' => $consultationData['rips_technology_purpose_id'],
                'rips_collection_concept_id' => $consultationData['rips_collection_concept_id'],
                'copayment_receipt_number' => $consultationData['copayment_receipt_number'],
                'service_value' => $consultationData['service_value'],
                'copayment_value' => $consultationData['copayment_value'],
            ]);

            $diagnoses = array_merge(
                collect($consultationData['principal_diagnoses'] ?? [])->map(function ($item) {
                    $item['sequence'] = 1;
                    return $item;
                })->toArray(),
                collect($consultationData['related_diagnoses'] ?? [])->map(function ($item, $key) {
                    $item['sequence'] = $key + 2;
                    $item['rips_diagnosis_type_id'] = null;
                    return $item;
                })->toArray()
            );

            foreach ($diagnoses as $diagnosis) {
                $consultation->diagnoses()->create($diagnosis);
            }
        }

        return $record;
    }
}
