<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RipsTechnologyPurpose extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name'];
}
