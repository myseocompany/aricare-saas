<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RipsPayerType extends Model
{
    use HasFactory;

    protected $table = 'rips_payer_types';

    protected $fillable = [
        'name',
        'description',
    ];

    public function payers()
    {
        return $this->hasMany(RipsPayer::class, 'type_id');
    }
}
