<?php

namespace App\Models\Rda;

use App\Models\Doctor;
use App\Models\Patient;
use App\Traits\PopulateTenantID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Encounter extends Model
{
    use BelongsToTenant, PopulateTenantID;

    protected $table = 'rda_encounters';

    protected $fillable = [
        'tenant_id',
        'patient_id',
        'doctor_id',
        'encounter_type_id',
        'start_at',
        'end_at',
        'reason',
        'vida_code',
        'status_id',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function encounterType(): BelongsTo
    {
        return $this->belongsTo(EncounterType::class, 'encounter_type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(EncounterStatus::class, 'status_id');
    }

    public function scopeForTenant(Builder $query, ?string $tenantId = null): Builder
    {
        $tenantId ??= auth()->user()?->tenant_id;

        if ($tenantId === null) {
            return $query;
        }

        return $query->where('tenant_id', $tenantId);
    }
}
