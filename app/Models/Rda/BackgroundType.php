<?php

namespace App\Models\Rda;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BackgroundType extends Model
{
    protected $table = 'rda_background_types';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function backgrounds(): HasMany
    {
        return $this->hasMany(PatientBackground::class, 'background_type_id');
    }
}
