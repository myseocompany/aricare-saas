<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;

class RipsPayer extends Model
{
    public function type()
    {
        return $this->belongsTo(RipsPayerType::class, 'type_id');
    }

}
