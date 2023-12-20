<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Tribunal extends Authenticatable
{
    use Notifiable;

    protected $guard = 'tribunal';



    protected $fillable = [
        'nombre', 'apellido', 'cod_carnet_identidad', 'usuario', 'password', 'tribunalactivo', 'usertype', 'created_at', 'updated_at',
    ];

    protected $hidden = [
        'remember_token',
    ];

    // Resto de tu lógica o relaciones si es necesario
}
