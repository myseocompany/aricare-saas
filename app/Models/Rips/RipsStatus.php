<?php

namespace App\Models\Rips;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RipsStatus extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'rips_statuses'; // Asegúrate de que el nombre de la tabla coincida con tu base de datos

    // Definir los campos que son asignables en masa
    protected $fillable = [
        'name', // Nombre del estado
        'description', // Descripción del estado
    ];

    // Si es necesario, define las relaciones con otros modelos
    // Por ejemplo, si RipsStatus tiene muchas instancias de RipsPatientService, puedes agregar esta relación
    public function patientServices()
    {
        return $this->hasMany(RipsPatientService::class, 'status_id');
    }
}
