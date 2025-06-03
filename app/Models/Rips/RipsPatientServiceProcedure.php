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
    public function cups()
    {
        return $this->belongsTo(RipsCups::class, 'rips_cups_id');
    }

    public function cie10()
    {
        return $this->belongsTo(Cie10::class, 'cie10_id');
    }

    public function surgeryCie10()
    {
        return $this->belongsTo(Cie10::class, 'surgery_cie10_id');
    }
}
