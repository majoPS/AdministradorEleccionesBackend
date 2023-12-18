<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;





class EleccionesFacCarr extends Model
{
    protected $table = 'elecciones_fac_carr';
    protected $primaryKey = null; // La tabla tiene una clave primaria compuesta

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'COD_ELECCION',
        'COD_FACULTAD',
        'COD_CARRERA',
    ];

    // Define las relaciones con los modelos Elecciones, Facultad y Carrera
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
}
