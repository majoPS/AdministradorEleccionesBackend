<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoblacionFacuCarr extends Model
{
    protected $table = 'poblacion_facu_carr';
    //protected $primaryKey = ['codsis', 'cod_facultad', 'cod_carrera'];
    public $incrementing = false;
    public $timestamps = false;
}
