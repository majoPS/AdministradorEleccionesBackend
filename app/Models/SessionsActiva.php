<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionsActiva extends Model
{
    protected $table = 'sessions';

    protected $fillable = [
        'id', 'user_id', 'ip_address', 'user_agent', 'payload', 'last_activity', 'user', 'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public $timestamps = false; // Desactivar timestamps para la tabla 'sessions'

    // Relación con el modelo Admin (si es necesario)
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }
}
