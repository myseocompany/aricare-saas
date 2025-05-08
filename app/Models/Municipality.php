<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\departmentCountry; // Asegúrate de importar el modelo correcto


class Municipality extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'country_id',
        'is_active'
    ];

    // Relación con deprtamento
    public function department()
    {
        return $this->belongsTo(DepartmentCountry::class, 'department_country_id');
    }
}
