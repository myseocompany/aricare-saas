<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RipsPatientServiceProcedure extends Model
{
    protected $fillable = [
        'patient_service_id',
        'mipres_id',
        'authorization_number',
        'rips_cups_id',
        'cie10_id',
        'surgery_cie10_id',
        'service_value',
        'copayment_value',
        'copayment_receipt_number',
    ];

    /**
     * Relación con el servicio RIPS al que pertenece este procedimiento.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(RipsPatientService::class, 'patient_service_id');
    }

    /**
     * Relación con el código CUPS del procedimiento.
     */
    public function ripsCups(): BelongsTo
    {
        return $this->belongsTo(Cups::class, 'rips_cups_id');
    }

    /**
     * Diagnóstico principal relacionado.
     */
    public function cie10(): BelongsTo
    {
        return $this->belongsTo(Cie10::class, 'cie10_id');
    }

    /**
     * Diagnóstico quirúrgico relacionado.
     */
    public function surgeryCie10(): BelongsTo
    {
        return $this->belongsTo(Cie10::class, 'surgery_cie10_id');
    }
}
