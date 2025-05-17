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
        'rips_service_group_id',
        'rips_service_id',
        'rips_technology_purpose_id',
        'rips_collection_concept_id',
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

        public function serviceGroup(): BelongsTo
    {
        return $this->belongsTo(RipsServiceGroup::class, 'rips_service_group_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(RipsService::class, 'rips_service_id');
    }

    public function technologyPurpose(): BelongsTo
    {
        return $this->belongsTo(RipsTechnologyPurpose::class, 'rips_technology_purpose_id');
    }

    public function collectionConcept(): BelongsTo
    {
        return $this->belongsTo(RipsCollectionConcept::class, 'rips_collection_concept_id');
    }



}
