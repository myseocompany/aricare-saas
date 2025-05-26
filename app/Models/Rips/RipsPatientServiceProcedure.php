<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;

class RipsPatientServiceProcedure extends Model
{
    protected $fillable = [
        'rips_patient_service_id',
        'mipres_id',
        'authorization_number',
        'rips_cups_id',
        'cie10_id',
        'surgery_cie10_id',
        'service_value',
        'copayment_value',
        'copayment_receipt_number',
    ];

    public function patientService()
    {
        return $this->belongsTo(RipsPatientService::class, 'rips_patient_service_id');
    }
}
