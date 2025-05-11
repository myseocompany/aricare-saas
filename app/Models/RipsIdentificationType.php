<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RipsIdentificationType extends Model
{
    protected $table = 'rips_identification_types';

    protected $fillable = ['code', 'name'];
}
