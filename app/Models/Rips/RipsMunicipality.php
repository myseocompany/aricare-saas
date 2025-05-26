<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RipsMunicipality extends Model
{
    protected $fillable = ['code', 'name', 'rips_department_id'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(RipsDepartment::class, 'rips_department_id');
    }
}
