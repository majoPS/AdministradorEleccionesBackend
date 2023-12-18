<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    protected $table = 'permisos';
    protected $primaryKey = 'id_permiso';
    public $timestamps = false; // Si no tienes timestamps (created_at, updated_at)

    protected $fillable = [
        'tipo_usuario',
        'cod_sis',
        'fecha_solicitud',
        'fecha_fin_solicitud',
        'fecha_permiso',
        'motivo',
        'cod_comite',
        'estado',
        'codsis_sustituto',
        'comprobante_entregado',
        'fecha_comprobante_entregado',
    ];

    // Puedes agregar relaciones aquí si es necesario
}
