<?php

namespace App\Models\Rips;

use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RipsPatientService extends Model
{
    // Tabla asociada
    protected $table = 'rips_patient_services';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'tenant_id',
        'location_code',
        'has_incapacity',
        'service_datetime',
    ];

    // Relación con el paciente
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    // Relación con el doctor
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function procedures()
    {
        return $this->hasMany(RipsPatientServiceProcedure::class, 'rips_patient_service_id');
    }

    public function consultations()
    {
        return $this->hasMany(RipsPatientServiceConsultation::class, 'rips_patient_service_id');
    }
    public function billingDocument()
    {
        return $this->belongsTo(\App\Models\Rips\RipsBillingDocument::class, 'billing_document_id');
    }

    // App\Models\Rips\RipsPatientService.php

    public function serviceGroupMode()
    {
        return $this->belongsTo(\App\Models\Rips\RipsServiceGroupMode::class, 'rips_service_group_mode_id');
    }

    public function serviceReason()
    {
        return $this->belongsTo(\App\Models\Rips\RipsServiceReason::class, 'rips_service_reason_id');
    }

    public function consultationCups()
    {
        return $this->belongsTo(\App\Models\Rips\RipsCups::class, 'rips_consultation_cups_id');
    }


}
