<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurado extends Model
{
    use HasFactory;
    protected $table = 'jurados'; 
    protected $primaryKey = 'COD_JURADO';
    protected $fillable = ['COD_JURADO', 'CARGO_JURADO','COD_MESA','COD_SIS'];

    
    public function mesa()
    {
        return $this->belongsTo(Mesas::class, 'COD_MESA');
    }

    public function poblacion()
    {
        return $this->belongsTo(Poblacion::class, 'COD_SIS');
    }
}
