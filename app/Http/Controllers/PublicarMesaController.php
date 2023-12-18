<?php

namespace App\Http\Controllers;

use App\Models\PublicarMesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicarMesaController extends Controller
{
    public function index()
    {
        // Obtiene todos los registros de mesas publicadas
        $publicaciones = PublicarMesa::all();

        return response()->json($publicaciones);
    }

    public function listaPublicarMesa()
    {
        $publicaciones = DB::table('publicar_mesas')
            ->select(
                'publicar_mesas.id_eleccion_mesa',
                'publicar_mesas.fecha_publicacion',
                'publicar_mesas.titulo',
                'publicar_mesas.contenido'
            )
            ->get();

        return response()->json($publicaciones);
    }

    public function store(Request $request)
    {
        $id_eleccion_mesa = $request->input('id_eleccion_mesa');
        $fechaPublicacion = $request->input('fecha_publicacion');
        $titulo = $request->input('titulo');
        $contenido = $request->input('contenido');

        $publicacion = new PublicarMesa();
        $publicacion->id_eleccion_mesa = $id_eleccion_mesa;
        $publicacion->fecha_publicacion = $fechaPublicacion;
        $publicacion->titulo = $titulo;
        $publicacion->contenido = $contenido;

        $publicacion->save();

        return response()->json(['message' => 'Publicación de mesa creada con éxito']);
    }

    public function show($id)
    {
        // Muestra una mesa publicada específica
        $publicacion = PublicarMesa::find($id);

        return response()->json($publicacion);
    }

    public function update(Request $request, $id)
    {
        // Actualiza una mesa publicada
        $publicacion = PublicarMesa::find($id);

        $data = $request->validate([
            'id_mesa' => 'required',
            'fecha_publicacion' => 'required',
            'titulo' => 'required',
            'contenido' => 'required',
        ]);

        $publicacion->update($data);

        return response()->json($publicacion, 200);
    }

    public function destroy($id)
    {
        // Elimina una mesa publicada
        $publicacion = PublicarMesa::find($id);
        $publicacion->delete();

        return response()->json(null, 204);
    }
}
