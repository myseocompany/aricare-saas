<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RipsPatientServiceConsultation extends Model
{
    protected $fillable = [
        'rips_patient_service_id',
        'rips_cups_id',
        'rips_service_group_id',
        'rips_service_id',
        'rips_technology_purpose_id',
        'service_value',
        'rips_collection_concept_id',
        'copayment_value',
        'copayment_receipt_number',
    ];

    /**
     * Relación con el servicio del paciente al que pertenece esta consulta.
     */
    
    public function ripsService(): BelongsTo
    {
        return $this->belongsTo(RipsService::class, 'rips_service_id');
    }


    public function ripsServiceGroup(): BelongsTo
    {
        return $this->belongsTo(RipsServiceGroup::class, 'rips_service_group_id');
    }


    public function ripsTechnologyPurpose(): BelongsTo
    {
        return $this->belongsTo(RipsTechnologyPurpose::class, 'rips_technology_purpose_id');
    }

    public function ripsCollectionConcept(): BelongsTo
    {
        return $this->belongsTo(RipsCollectionConcept::class, 'rips_collection_concept_id');
    }


    public function diagnoses()
    {
        return $this->hasMany(\App\Models\RipsPatientServiceConsultationDiagnosis::class, 'rips_patient_service_consultation_id');
    }

    public function ripsPatientService()
    {
        return $this->belongsTo(RipsPatientService::class, 'rips_patient_service_id');
    }

    public function ripsCups(): BelongsTo
    {
        return $this->belongsTo(RipsCups::class, 'rips_cups_id');
    }





}
