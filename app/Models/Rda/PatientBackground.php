<?php

namespace App\Models\Rda;

use App\Models\Patient;
use App\Models\Rips\RipsCups;
use App\Models\Rips\Cie10;
use App\Models\User;
use App\Traits\PopulateTenantID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class PatientBackground extends Model
{
    use BelongsToTenant, PopulateTenantID;

    protected $table = 'rda_patient_backgrounds';

    protected $fillable = [
        'tenant_id',
        'patient_id',
        'background_type_id',
        'description',
        'cie10_id',
        'rips_cups_id',
        'medication_name',
        'procedure_name',
        'related_person',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function backgroundType(): BelongsTo
    {
        return $this->belongsTo(BackgroundType::class, 'background_type_id');
    }

    public function cie10(): BelongsTo
    {
        return $this->belongsTo(Cie10::class, 'cie10_id');
    }

    public function cups(): BelongsTo
    {
        return $this->belongsTo(RipsCups::class, 'rips_cups_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeForTenant(Builder $query, ?string $tenantId = null): Builder
    {
        $tenantId ??= auth()->user()?->tenant_id;

        if (empty($tenantId)) {
            return $query;
        }

        return $query->where('tenant_id', $tenantId);
    }
}
