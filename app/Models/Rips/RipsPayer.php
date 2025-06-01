<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class RipsPayer extends Model
{
    use BelongsToTenant; // 👈 Importante si usas tenancy de Stancl

    protected $table = 'rips_payers';

    protected $fillable = [
        'tenant_id',
        'type_id',
        'name',
        'identification',
        'address',
        'phone',
        'email',
    ];

    public function type()
    {
        return $this->belongsTo(RipsPayerType::class, 'type_id');
    }

    public function agreements()
    {
        return $this->hasMany(RipsTenantPayerAgreement::class, 'payer_id');
    }

    
}
