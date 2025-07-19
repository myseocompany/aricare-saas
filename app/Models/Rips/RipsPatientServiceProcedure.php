<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RipsPatientServiceProcedure extends Model
{
    protected $fillable = [
        'rips_patient_service_id',
        'rips_admission_route_id',
        'rips_service_group_mode_id',
        'rips_service_group_id',
        'rips_service_id',
        'rips_collection_concept_id',
        'mipres_id',
        'rips_technology_purpose_id',
        'authorization_number',
        'rips_cups_id',
        'cie10_id',
        'surgery_cie10_id',
        'rips_complication_cie10_id',
        'service_value',
        'copayment_value',
        'copayment_receipt_number',
    ];

    public function patientService(): BelongsTo
    {
        return $this->belongsTo(RipsPatientService::class, 'rips_patient_service_id');
    }

    public function admissionRoute(): BelongsTo
    {
        return $this->belongsTo(RipsAdmissionRoute::class, 'rips_admission_route_id');
    }

    public function serviceGroupMode(): BelongsTo
    {
        return $this->belongsTo(RipsServiceGroupMode::class, 'rips_service_group_mode_id');
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

    public function cups(): BelongsTo
    {
        return $this->belongsTo(RipsCups::class, 'rips_cups_id');
    }

    public function cie10(): BelongsTo
    {
        return $this->belongsTo(Cie10::class, 'cie10_id');
    }

    public function surgeryCie10(): BelongsTo
    {
        return $this->belongsTo(Cie10::class, 'surgery_cie10_id');
    }

    public function complicationCie10(): BelongsTo
    {
        return $this->belongsTo(Cie10::class, 'rips_complication_cie10_id');
    }
}
