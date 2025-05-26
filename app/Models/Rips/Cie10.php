<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;

class Cie10 extends Model
{
    protected $table = 'cie10';

    protected $fillable = [
        'code',
        'description',
    ];
}
