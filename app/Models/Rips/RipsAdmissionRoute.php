<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;

class RipsAdmissionRoute extends Model
{
    protected $table = 'rips_admission_routes';

    protected $fillable = ['code','name'];
}
