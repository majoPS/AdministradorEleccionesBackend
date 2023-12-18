<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Poblacion extends Model
{
    use HasFactory,Notifiable;
    public $incrementing = false;
    protected $keyType = '';
    public $timestamps = false;
    protected $table = 'poblacion';
    protected $primaryKey = 'CODSIS';
    protected $fillable = [
        'COD_CANDIDATO',
        'CODCOMITE',
        'NOMBRE',
        'APELLIDO',
        'CARNETIDENTIDAD',
        'APELLIDOS',
        'ESTUDIANTE',
        'DOCENTE',
        'EMAIL'
    ];

    protected $guarded = [];

    public function routeNotificationForMail($notification)
    {
    
        return $this->EMAIL;
 
    }

    /*public function tribunalElectoralUniversitario(){
        return $this->belongsTo(TribunalElectoralUniversitario::class, 'COD_TEU');
    }*/

    public function carrera(){
        return $this->belongsTo(Carrera::class, 'COD_CARRERA');
    }

    public function jurado(){
        return $this->hasOne(Jurado::class, 'COD_JURADO');
    }

    public function comiteElectoral(){
        return $this->belongsTo(Comite_Electoral::class, 'COD_COMITE');
    }

    public function candidato(){
        return $this->hasOne(Candidato::class, 'COD_CANDIDATO');
    }

    public function titularesSuplentes()
    {
        return $this->hasMany(AsociarTitularSuplente::class, 'COD_SIS', 'CODSIS');
}
}