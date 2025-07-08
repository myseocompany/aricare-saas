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
        'provider_code',
        'rips_idsispro',
        'rips_passispro',
        // Agrega más campos si lo necesitas
    ];

    public $incrementing = false; // porque el ID es varchar
    protected $keyType = 'string'; // especifica que no es integer
}
