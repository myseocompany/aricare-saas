<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RipsPatientServiceConsultationDiagnosis extends Model
{
    protected $fillable = ['patient_service_id', 'cie10_id', 'sequence'];

    public function service() {
        return $this->belongsTo(RipsPatientService::class, 'patient_service_id');
    }
}
