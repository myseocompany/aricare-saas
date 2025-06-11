<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RipsBillingDocument extends Model
{
    use HasFactory;

    protected $table = 'rips_billing_documents';

    protected $fillable = [
        'tenant_id',
        'type_id',
        'document_number',
        'issued_at',
        'cufe',
        'agreement_id',
        'uuid_dian',
        'total_amount',
        'copay_amount',
        'discount_amount',
        'net_amount',
        'xml_path',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'copay_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function patientServices()
    {
        return $this->hasMany(RipsPatientService::class, 'billing_document_id');
    }

    public function agreement()
    {
        return $this->belongsTo(RipsTenantPayerAgreement::class, 'agreement_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

}
