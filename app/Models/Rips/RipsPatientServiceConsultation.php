<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;

class RipsPatientServiceConsultation extends Model
{
    protected $table = 'rips_patient_service_consultations';

    // Campos que se pueden asignar masivamente
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

    // Relación con RipsPatientService
    public function patientService()
    {
        return $this->belongsTo(RipsPatientService::class, 'rips_patient_service_id');
    }

    // Relación con RipsCups
    public function cups()
    {
        return $this->belongsTo(RipsCups::class, 'rips_cups_id');
    }

    // Relación con RipsServiceGroup
    public function serviceGroup()
    {
        return $this->belongsTo(RipsServiceGroup::class, 'rips_service_group_id');
    }

    // Relación con RipsService
    public function service()
    {
        return $this->belongsTo(RipsService::class, 'rips_service_id');
    }

    // Relación con RipsTechnologyPurpose
    public function technologyPurpose()
    {
        return $this->belongsTo(RipsTechnologyPurpose::class, 'rips_technology_purpose_id');
    }

    // Relación con RipsCollectionConcept
    public function collectionConcept()
    {
        return $this->belongsTo(RipsCollectionConcept::class, 'rips_collection_concept_id');
    }

    public function diagnoses()
    {
        return $this->hasMany(RipsPatientServiceConsultationDiagnosis::class, 'rips_patient_service_consultation_id');
    }

}
