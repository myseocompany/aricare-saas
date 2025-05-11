<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RipsDepartment extends Model
{
    protected $fillable = ['code', 'name', 'rips_country_id'];

    public function ripsMunicipalities(): HasMany
    {
        return $this->hasMany(RipsMunicipality::class);
    }

    public function ripsCountry(): BelongsTo
    {
        return $this->belongsTo(RipsCountry::class);
    }
}
