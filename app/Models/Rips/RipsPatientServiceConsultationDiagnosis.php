<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;

class RipsPatientServiceConsultationDiagnosis extends Model
{
    protected $table = 'rips_patient_service_consultation_diagnoses';

    protected $fillable = [
        'rips_patient_service_consultation_id',
        'cie10_id',
        'rips_diagnosis_type_id',
        'sequence',
    ];



    public function cie10()
    {
        return $this->belongsTo(Cie10::class, 'cie10_id');
    }

    public function diagnosisType()
    {
        return $this->belongsTo(RipsDiagnosisType::class, 'rips_diagnosis_type_id');
    }
}
