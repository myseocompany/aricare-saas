<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RipsPatientServiceConsultation extends Model
{
    protected $table = 'rips_patient_service_consultations';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'rips_patient_service_id',
        'rips_cups_id',
        'rips_service_group_id',
        'rips_service_group_mode_id',
        'rips_service_reason_id',
        'rips_consultation_cups_id',
        'rips_service_id',
        'rips_technology_purpose_id',
        'service_value',
        'rips_collection_concept_id',
        'copayment_value',
        'copayment_receipt_number',
    ];

    // Relaciones
    public function patientService(): BelongsTo
    {
        return $this->belongsTo(RipsPatientService::class, 'rips_patient_service_id');
    }

    public function cups(): BelongsTo
    {
        return $this->belongsTo(RipsCups::class, 'rips_cups_id');
    }

    public function serviceGroup(): BelongsTo
    {
        return $this->belongsTo(RipsServiceGroup::class, 'rips_service_group_id');
    }

    public function serviceGroupMode(): BelongsTo
    {
        return $this->belongsTo(RipsServiceGroupMode::class, 'rips_service_group_mode_id');
    }

    public function serviceReason(): BelongsTo
    {
        return $this->belongsTo(RipsServiceReason::class, 'rips_service_reason_id');
    }

    public function consultationCups(): BelongsTo
    {
        return $this->belongsTo(RipsCups::class, 'rips_consultation_cups_id');
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

    // Diagnósticos
    public function diagnoses(): HasMany
    {
        return $this->hasMany(RipsPatientServiceConsultationDiagnosis::class);
    }

    public function principalDiagnoses(): HasMany
    {
        return $this->diagnoses()->where('sequence', 1);
    }

    public function relatedDiagnoses(): HasMany
    {
        return $this->diagnoses()->where('sequence', '>', 1);
    }
}
