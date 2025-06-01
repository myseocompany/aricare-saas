<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RipsPatientService extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'location_code',
        'has_incapacity', 'service_datetime', 'service_group_code',
        'service_code', 'technology_purpose_code', 'collection_concept_code',
        'billing_document_id', 'tenant_id'
    ];

    public function diagnoses() {
        return $this->hasMany(RipsPatientServiceDiagnosis::class, 'patient_service_id');
    }

    public function procedures() {
        return $this->hasMany(RipsPatientServiceProcedure::class, 'patient_service_id');
    }

    public function consultation() {
        return $this->hasOne(RipsPatientServiceConsultation::class, 'patient_service_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function billingDocument()
    {
        return $this->belongsTo(\App\Models\Rips\RipsBillingDocument::class, 'billing_document_id');
    }


}
