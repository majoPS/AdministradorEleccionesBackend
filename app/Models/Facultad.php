<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facultad extends Model
{
    protected $table = 'facultad';
    protected $primaryKey = 'COD_FACULTAD';
    public $timestamps = false;

    protected $fillable = [
        'NOMBRE_FACULTAD',
        'DESCRIPCION',
    ];
}
