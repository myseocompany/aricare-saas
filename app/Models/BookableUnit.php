<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PopulateTenantID;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BookableUnit extends Model
{
    use BelongsToTenant, PopulateTenantID;

    protected $fillable = [
        'name',
        'description',
        'is_available',
        'tenant_id',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'is_available' => 'boolean',
    ];
}
