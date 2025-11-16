<?php

namespace App\Models\Rda;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RdaOccupation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'major_group_code',
        'major_group_name',
        'subgroup_code',
        'subgroup_name',
        'is_active',
    ];
}
