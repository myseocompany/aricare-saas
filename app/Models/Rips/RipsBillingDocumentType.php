<?php 
namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;

class RipsBillingDocumentType extends Model
{
    protected $table = 'rips_billing_document_types';

    protected $fillable = [
        'name',
        'description',
    ];
}
