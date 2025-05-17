<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RipsServiceReason extends Model
{
    protected $table = 'rips_service_reason';

    protected $fillable = ['code','name'];
}
