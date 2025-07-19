<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RipsPatientServiceTemplateConsultationDiagnosis extends Model
{
    use HasFactory;

    protected $fillable = [
        'rips_patient_service_template_consultation_id',
        'cie10_id',
        'rips_diagnosis_type_id',
        'sequence',
    ];

    public function consultation()
    {
        return $this->belongsTo(RipsPatientServiceTemplateConsultation::class, 'rips_patient_service_template_consultation_id');
    }
}
