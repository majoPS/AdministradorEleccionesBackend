<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicarConvocatoria extends Model
{
 
  
        protected $table = 'publicar_convocatoria';
        public $timestamps = false;
    
        protected $fillable = [
            'id_convocatoria',
            'fecha_publicacion',
            'titulo',
            'contenido'
        ];
        public function convocatoria()
        {
            return $this->belongsTo(ConvocatoriaElecciones::class, 'id_convocatoria');
        }
    
}
