<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaVotante  extends Model
{
    use HasFactory;
    protected $table = 'listas_votantes';
    protected $primaryKey = 'COD_LISTAVOTANTES';
    public $timestamps = false;

    protected $fillable = [
        'cod_mesa',
        'carnetidentidad',
        'firma',
    ];

}
