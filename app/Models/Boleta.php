<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boleta extends Model
{
    protected $table = 'boletas';

    protected $fillable = [
        'COD_ELECCION',
        'COD_FRENTE',
        'COD_CANDIDATO',
        'NOMBRE_UNIVERSIDAD',
        'NOMBRE_FRENTE',
        'NOMBRE_CANDIDATO',
        'COD_CARNETIDENTIDAD',
        'CARGO_POSTULADO',
        'HABILITADO',
    ];

    public $timestamps = false; // Desactivar timestamps
}
