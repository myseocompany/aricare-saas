<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoIdentificacionPisis extends Model
{
    protected $table = 'tipos_identificacion_pisis';

    protected $fillable = ['codigo', 'nombre'];
}
