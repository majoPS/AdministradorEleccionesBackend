<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comite_Electoral extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $guarded = [];


    protected $table = 'comiteelectoral'; // Nombre de la tabla en la base de datos

    protected $primaryKey = 'CODCOMITE'; // Clave primaria de la tabla

    protected $fillable = [
        'CODCOMITE',
        'CARGOCOMITE',
        'FECHACOMITE',
    ];
}
