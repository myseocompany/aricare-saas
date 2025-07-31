<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RipsTenantPayerAgreement extends Model
{
    use HasFactory;

    protected $table = 'rips_tenant_payer_agreements';

    protected $fillable = [
        'name',
        'code',
    ];

    public function payer()
    {
        return $this->belongsTo(RipsPayer::class, 'payer_id');
    }
    
    
}
