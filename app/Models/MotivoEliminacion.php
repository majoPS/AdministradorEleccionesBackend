<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoEliminacion extends Model
{
    protected $table = 'motivos_eliminacion';
    protected $primaryKey = 'COD_MOTIVO';
    protected $fillable = ['MOTIVO'];

    public function frentes()
    {
        return $this->hasMany(Frente::class, 'COD_MOTIVO');
    }

}
