<?php 

namespace App\Actions\Rips;

use Illuminate\Support\Facades\Auth;
use App\Models\Rips\RipsPatientService;
use App\Models\Rips\RipsPatientServiceTemplate;

class CreateServiceTemplateFromService
{
    public function __invoke(RipsPatientService $service, string $templateName): RipsPatientServiceTemplate
    {
        $template = RipsPatientServiceTemplate::create([
            'name' => $templateName,
            'description' => 'Plantilla generada desde servicio ID ' . $service->id,
            'tenant_id' => $service->tenant_id,
            'user_id' => Auth::id(),
            'is_public' => false,
        ]);

        foreach ($service->consultations ?? [] as $consultation) {
            $template->consultations()->create([
                'rips_cups_id' => $consultation->rips_cups_id,
                'rips_service_group_id' => $consultation->rips_service_group_id,
                'rips_service_group_mode_id' => $consultation->rips_service_group_mode_id,
                'rips_service_reason_id' => $consultation->rips_service_reason_id,
                'rips_consultation_cups_id' => $consultation->rips_consultation_cups_id,
                'rips_service_id' => $consultation->rips_service_id,
                'rips_technology_purpose_id' => $consultation->rips_technology_purpose_id,
                'service_value' => $consultation->service_value,
                'rips_collection_concept_id' => $consultation->rips_collection_concept_id,
                'copayment_value' => $consultation->copayment_value,
                'copayment_receipt_number' => $consultation->copayment_receipt_number,
            ]);
        }

        foreach ($service->consultations ?? [] as $consultation) {
            $templateConsultation = $template->consultations()->create([
                'rips_cups_id' => $consultation->rips_cups_id,
                'rips_service_group_id' => $consultation->rips_service_group_id,
                'rips_service_group_mode_id' => $consultation->rips_service_group_mode_id,
                'rips_service_reason_id' => $consultation->rips_service_reason_id,
                'rips_consultation_cups_id' => $consultation->rips_consultation_cups_id,
                'rips_service_id' => $consultation->rips_service_id,
                'rips_technology_purpose_id' => $consultation->rips_technology_purpose_id,
                'service_value' => $consultation->service_value,
                'rips_collection_concept_id' => $consultation->rips_collection_concept_id,
                'copayment_value' => $consultation->copayment_value,
                'copayment_receipt_number' => $consultation->copayment_receipt_number,
            ]);

            foreach ($consultation->diagnoses ?? [] as $diagnosis) {
                $templateConsultation->diagnoses()->create([
                    'cie10_id' => $diagnosis->cie10_id,
                    'rips_diagnosis_type_id' => $diagnosis->rips_diagnosis_type_id,
                    'sequence' => $diagnosis->sequence,
                ]);
            }
        }


        foreach ($service->procedures ?? [] as $procedure) {
            $template->procedures()->create([
                'rips_admission_route_id' => $procedure->rips_admission_route_id,
                'rips_service_group_mode_id' => $procedure->rips_service_group_mode_id,
                'rips_service_group_id' => $procedure->rips_service_group_id,
                'rips_service_id' => $procedure->rips_service_id,
                'rips_collection_concept_id' => $procedure->rips_collection_concept_id,
                'rips_technology_purpose_id' => $procedure->rips_technology_purpose_id,
                'mipres_id' => $procedure->mipres_id,
                'authorization_number' => $procedure->authorization_number,
                'rips_cups_id' => $procedure->rips_cups_id,
                'cie10_id' => $procedure->cie10_id,
                'surgery_cie10_id' => $procedure->surgery_cie10_id,
                'rips_complication_cie10_id' => $procedure->rips_complication_cie10_id,
                'service_value' => $procedure->service_value,
                'copayment_value' => $procedure->copayment_value,
                'copayment_receipt_number' => $procedure->copayment_receipt_number,
            ]);
        }

        return $template;
    }
}
