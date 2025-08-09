<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
   protected $table = 'tenants';

    protected $fillable = [
        'tenant_username',
        'hospital_name',
        'document_number',
        'document_type',
        'is_billing_enabled',
        'provider_code',
        'tax_identifier',
        'sispro_username',
        'rips_idsispro',
        'rips_passispro',
        'sispro_password',
        'location_code',
        'data',
        'rips_identification_type_id',
        'rips_identification_number',
        'rips_provider_code'
    ];

    public $incrementing = false; // porque el ID es varchar
    protected $keyType = 'string'; // especifica que no es integer
}
