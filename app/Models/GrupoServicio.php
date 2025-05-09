<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoServicio extends Model
{
    protected $table = 'grupo_servicios';

    protected $fillable = ['codigo', 'nombre'];
}
