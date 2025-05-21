<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RipsPatientServiceConsultation extends Model
{
    protected $fillable = [
        'patient_service_id',
        'consultation_cups_id',
        'service_value',
        'copayment_value',
        'copayment_receipt_number',
        'rips_service_group_id',
        'rips_service_id',
        'rips_technology_purpose_id',
        'rips_collection_concept_id',
    ];

    /**
     * Relación con el servicio del paciente al que pertenece esta consulta.
     */
    
    public function service(): BelongsTo
    {
        return $this->belongsTo(RipsService::class, 'rips_service_id');
    }

    /**
     * Relación con el código CUPS de la consulta.
     */
    public function consultationCups(): BelongsTo
    {
        return $this->belongsTo(Cups::class, 'consultation_cups_id');
    }

    public function serviceGroup(): BelongsTo
    {
        return $this->belongsTo(RipsServiceGroup::class, 'rips_service_group_id');
    }



    public function technologyPurpose(): BelongsTo
    {
        return $this->belongsTo(RipsTechnologyPurpose::class, 'rips_technology_purpose_id');
    }

    public function collectionConcept(): BelongsTo
    {
        return $this->belongsTo(RipsCollectionConcept::class, 'rips_collection_concept_id');
    }


    public function diagnoses()
    {
        return $this->hasMany(\App\Models\RipsPatientServiceConsultationDiagnosis::class, 'rips_patient_service_consultation_id');
    }

}
