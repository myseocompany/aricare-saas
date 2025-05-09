<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModalidadAtencion extends Model
{
    protected $table = 'modalidades_atencion';

    protected $fillable = ['codigo', 'nombre'];
}

