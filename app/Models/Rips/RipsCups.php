<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RipsCups extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'group',
        'subgroup_code',
    ];

    /**
     * Relación con procedimientos asociados a este código CUPS.
     */
    public function procedures(): HasMany
    {
        return $this->hasMany(RipsPatientServiceProcedure::class, 'rips_cups_id');
    }

    /**
     * Relación con consultas asociadas a este código CUPS.
     */
    public function consultations(): HasMany
    {
        return $this->hasMany(RipsPatientServiceConsultation::class, 'rips_cups_id');
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
