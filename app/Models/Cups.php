<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cups extends Model
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
        return $this->hasMany(RipsPatientServiceProcedure::class, 'cups_id');
    }

    /**
     * Relación con consultas asociadas a este código CUPS.
     */
    public function consultations(): HasMany
    {
        return $this->hasMany(RipsPatientServiceConsultation::class, 'consultation_cups_id');
    }
}
