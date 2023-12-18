<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    use HasFactory;

    protected $table = 'administrador'; 
    protected $primaryKey = 'CODADMINISTRADOR';
    public $incrementing = true; 
    protected $keyType = 'integer'; 

    protected $fillable = [
        'CODCOMITE',
        'nombreadministrador',
        'contrasenaadministrador',
        'correo',
        'telefono',
    ];
}
