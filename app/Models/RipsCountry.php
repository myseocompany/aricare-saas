<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RipsCountry extends Model
{
    protected $fillable = [
        'name',
        'alpha2',
        'alpha3',
    ];
}
