<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RipsService extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'rips_service_group_id'];

    public function group()
    {
        return $this->belongsTo(RipsServiceGroup::class, 'rips_service_group_id');
    }
}
