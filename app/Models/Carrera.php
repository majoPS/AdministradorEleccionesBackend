<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carrera';
    protected $primaryKey = 'COD_CARRERA';
    public $timestamps = false;

    protected $fillable = [
        'COD_FACULTAD',
        'NOMBRE_CARRERA',
        'DESCRIPCION'
    ];

    public function facultad()
    {
        return $this->belongsTo(Facultad::class, 'COD_FACULTAD');
    }

    public function frentes()
    {
        return $this->hasMany(Frente::class, 'COD_CARRERA');
    }
}
