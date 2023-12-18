<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcesoElectoral extends Model
{
    use HasFactory;
    protected $table = "procesoelectoral";
    protected $primaryKey = "CODPROCESOELECTORAL";
    protected $fillable = [
        "CODPROCESOELECTORAL",
        "CODADMINISTRADOR",
        "CARGO",
        "FECHAINICIOCONVOCATORIA",
        "FECHAFINCONVOCATORIA",
        "FECHAELECCIONES",
        "TIPOELECCIONES",
        "CONVOCATORIA"
    ];

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}
