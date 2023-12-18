<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Elecciones extends Model
{
    protected $table = 'elecciones'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'COD_ELECCION'; // Clave primaria
    public $timestamps = false; // Habilitar los campos created_at y updated_at

    protected $fillable = [
        'COD_ADMIN',
        'COD_FRENTE',
        'COD_TEU',
        'COD_COMITE',
        'MOTIVO_ELECCION',
        'TIPO_ELECCION',
        'FECHA_ELECCION',
        'FECHA_INI_CONVOCATORIA',
        'FECHA_FIN_CONVOCATORIA',
        'ELECCION_ACTIVA',
    ];

    public function frente()
    {
        return $this->belongsTo(Frente::class, 'COD_FRENTE');
    }

  
    public function jurados()
    {
        return $this->hasManyThrough(Jurado::class, Mesas::class,'COD_ELECCION','COD_MESA');
    }

    public function titularesSuplentes()
    {
        return $this->hasMany(AsociarTitularSuplente::class, 'COD_COMITE', 'COD_COMITE');
}
}