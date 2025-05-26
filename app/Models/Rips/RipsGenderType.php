<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;

class RipsGenderType extends Model
{
    protected $table = 'rips_gender_types';

    protected $fillable = ['code', 'name'];
}
