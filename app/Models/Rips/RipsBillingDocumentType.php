<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RipsBillingDocumentType extends Model
{
    use HasFactory;

    protected $table = 'rips_billing_document_types';

    protected $fillable = [
        'name',
        'description',
    ];

    public function billingDocuments()
    {
        return $this->hasMany(RipsBillingDocument::class, 'type_id');
    }
}
