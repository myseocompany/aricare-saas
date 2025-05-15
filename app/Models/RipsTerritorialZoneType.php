<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RipsTerritorialZoneType extends Model
{
    //
    protected $table = 'rips_territorial_zone_types';

    protected $fillable = ['code', 'name'];
}