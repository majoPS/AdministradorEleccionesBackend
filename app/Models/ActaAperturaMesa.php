<?php
// app\Models\ActaAperturaMesa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActaAperturaMesa extends Model
{
    protected $table = 'acta_apertura_mesas';
    protected $primaryKey = 'cod_acta_apertura';
    public $timestamps = false;

    protected $fillable = [
        'cod_acta_apertura',
        'cod_mesa',
        'hora_instalacion_mesa',
        'hora_inicio_votacion',
        'hora_fin_votacion',
        'dia_instalacion_mesa',
        'miembros',
        'tipo_eleccion',
    ];

    // Define las relaciones con otras tablas si es necesario
}
