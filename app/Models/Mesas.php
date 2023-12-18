<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesas extends Model
{
    protected $table = 'mesas';
    protected $primaryKey = 'COD_MESA';
    public $timestamps = false;

    protected $fillable = [
        'COD_ELECCION',
        'COD_FACULTAD',
        'COD_CARRERA',
        'NUM_MESA',
        'CANT_EST_MESA',
        'APELLIDOS_ESTUDIANTES',
    ];

    public function eleccion()
    {
        return $this->belongsTo(Elecciones::class, 'COD_ELECCION');
    }


    // Si hay relaciones, se pueden definir aquí
    // Por ejemplo, relación con la tabla Elecciones, Facultad, Carrera, etc.
    // Ejemplo:
    /*
    public function eleccion()
    {
        return $this->belongsTo(Elecciones::class, 'COD_ELECCION', 'COD_ELECCION');
    }

    public function facultad()
    {
        return $this->belongsTo(Facultad::class, 'COD_FACULTAD', 'COD_FACULTAD');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'COD_CARRERA', 'COD_CARRERA');
    }
    */
}
