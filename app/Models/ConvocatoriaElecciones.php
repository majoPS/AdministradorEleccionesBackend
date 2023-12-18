<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConvocatoriaElecciones extends Model
{
    protected $table = 'convocatoria_elecciones';
    protected $primaryKey = 'id_convocatoria';
    public $timestamps = false;

    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'motivo',
        'descripcion',
        'requisitos',
        'id_eleccion',
        'tipo',
        'candidatos',
        'estado',
        'restricciones',
        'contacto',
        'lugar'
    ];
}
