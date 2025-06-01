<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RipsDiagnosisType extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name'];
}
