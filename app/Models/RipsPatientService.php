<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RipsPatientService extends Model
{
    protected $fillable = [
        'patient_id',
        'tenant_id',
        'doctor_id',
        'location_code',
        'has_incapacity',
        'service_datetime',

    ];


    public function consultations()
{
    return $this->hasMany(\App\Models\RipsPatientServiceConsultation::class, 'rips_patient_service_id');
}

public function procedures()
{
    return $this->hasMany(\App\Models\RipsPatientServiceProcedure::class, 'rips_patient_service_id');
}

public function diagnoses()
{
    return $this->hasMany(\App\Models\RipsPatientServiceConsultationDiagnosis::class, 'rips_patient_service_id');
}


    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    


}
