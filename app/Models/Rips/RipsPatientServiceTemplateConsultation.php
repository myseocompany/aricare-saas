<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Rips\RipsPatientServiceTemplateConsultationDiagnosis;

class RipsPatientServiceTemplateConsultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'rips_patient_service_template_id',
        'rips_cups_id',
        'rips_service_group_id',
        'rips_service_group_mode_id',
        'rips_service_reason_id',
        'rips_consultation_cups_id',
        'rips_service_id',
        'rips_technology_purpose_id',
        'rips_collection_concept_id',
        'copayment_receipt_number',
        'service_value',
        'copayment_value',
    ];

    public function template()
    {
        return $this->belongsTo(RipsPatientServiceTemplate::class, 'rips_patient_service_template_id');
    }


    public function diagnoses()
    {
        return $this->hasMany(RipsPatientServiceTemplateConsultationDiagnosis::class, 'consultation_id');
    }

}
