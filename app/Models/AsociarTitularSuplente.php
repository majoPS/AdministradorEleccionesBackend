<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsociarTitularSuplente extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'asociartitularsuplente';
    protected $keyType = 'integer';
    protected $primaryKey = 'ID_TS';

    protected $fillable = [
        'ID_TS',
        'COD_SIS',
        'COD_COMITE',
        'COD_TITULAR_SUPLENTE',
    ];

    public function poblacion()
    {
        return $this->belongsTo(Poblacion::class, 'COD_SIS', 'CODSIS');
    }

}