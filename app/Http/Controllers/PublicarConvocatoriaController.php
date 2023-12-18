<?php

namespace App\Http\Controllers;
use App\Models\PublicarConvocatoria;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class PublicarConvocatoriaController extends Controller
{
    public function index()
    {
        // Obtiene todos los registros de convocatorias publicadas
        $publicaciones = PublicarConvocatoria::all();

        return response()->json($publicaciones);
    }



    public function listaPublicarConvocatoria()
    {
        $publicaciones = DB::table('publicar_convocatoria')
            ->join('convocatoria_elecciones', 'publicar_convocatoria.id_convocatoria', '=', 'convocatoria_elecciones.id_convocatoria')
            ->join('elecciones', 'convocatoria_elecciones.id_eleccion', '=', 'elecciones.COD_ELECCION')
            ->select(
                'publicar_convocatoria.fecha_publicacion',
                'elecciones.motivo_eleccion',
                'publicar_convocatoria.titulo',
                'convocatoria_elecciones.id_convocatoria'
            )
            ->get();

        return response()->json($publicaciones);
    }

    public function store(Request $request)
    {
        $idConvocatoria = $request->input('id_convocatoria');
        $fechaPublicacion = $request->input('fecha_publicacion');
        $titulo = $request->input('titulo');
        $contenido = $request->input('contenido');

        $publicacion = new PublicarConvocatoria();
        $publicacion->id_convocatoria = $idConvocatoria;
        $publicacion->fecha_publicacion = $fechaPublicacion;
        $publicacion->titulo = $titulo;
        $publicacion->contenido = $contenido;

        $publicacion->save();

        return response()->json(['message' => 'Publicación de convocatoria creada con éxito']);
    }

    public function show($id)
    {
        // Muestra una convocatoria publicada específica
        $publicacion = PublicarConvocatoria::find($id);

        return response()->json($publicacion);
    }

    public function update(Request $request, $id)
    {
        // Actualiza una convocatoria publicada
        $publicacion = PublicarConvocatoria::find($id);

        $data = $request->validate([
            'id_convocatoria' => 'required',
            'fecha_publicacion' => 'required',
            'titulo' => 'required',
            'contenido' => 'required',
        ]);

        $publicacion->update($data);

        return response()->json($publicacion, 200);
    }

    public function destroy($id)
    {
        // Elimina una convocatoria publicada
        $publicacion = PublicarConvocatoria::find($id);
        $publicacion->delete();

        return response()->json(null, 204);
    }
}
