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
    ];

    /**
     * Relación con el servicio del paciente al que pertenece esta consulta.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(RipsPatientService::class, 'patient_service_id');
    }

    /**
     * Relación con el código CUPS de la consulta.
     */
    public function consultationCups(): BelongsTo
    {
        return $this->belongsTo(Cups::class, 'consultation_cups_id');
    }
}
