<?php

namespace App\Models\Rips;

use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

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
        'requires_fev',
        'status_id',
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

    public function status(): BelongsTo
    {
        return $this->belongsTo(RipsStatus::class, 'status_id');
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
    
    
    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('tenant_id', Auth::user()->tenant_id);
            }
        });
    }


}
