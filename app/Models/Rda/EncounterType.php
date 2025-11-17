<?php

namespace App\Models\Rda;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EncounterType extends Model
{
    protected $table = 'rda_encounter_types';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function encounters(): HasMany
    {
        return $this->hasMany(Encounter::class, 'encounter_type_id');
    }
}
