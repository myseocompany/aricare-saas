<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;

class RipsServiceReason extends Model
{
    protected $table = 'rips_service_reasons';

    protected $fillable = ['code','name'];
}
