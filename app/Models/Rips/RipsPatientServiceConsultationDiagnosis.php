<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;

class RipsPatientServiceConsultationDiagnosis extends Model
{
    protected $table = 'rips_patient_service_consultation_diagnoses';

    protected $fillable = [
        'rips_patient_service_consultation_id',
        'cie10_id',
        'sequence',
    ];

    public function consultation()
    {
        return $this->belongsTo(RipsPatientServiceConsultation::class, 'rips_patient_service_consultation_id');
    }

    public function cie10()
    {
        return $this->belongsTo(Cie10::class, 'cie10_id');
    }
}
