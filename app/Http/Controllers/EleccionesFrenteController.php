<?php

namespace App\Http\Controllers;

use App\Models\EleccionesFrente;
use Illuminate\Http\Request;
use App\Models\Frente;
use Illuminate\Support\Facades\DB;

class EleccionesFrenteController extends Controller
{

    public function index()
    {
        $eleccionesFrentes = EleccionesFrente::all();

        return response()->json($eleccionesFrentes, 200);
    }
    public function store(Request $request)
    {
        // Valida y guarda los datos en la tabla Elecciones_Frente
        $data = $request->validate([
            'COD_ELECCION' => 'required|numeric',
            'COD_FRENTE' => 'required|numeric',
            // Agrega aquí otras validaciones si es necesario
        ]);

        $eleccionFrente = EleccionesFrente::create($data);

        return response()->json($eleccionFrente, 201);
    }

    public function guardarRelacionEleccionesFrente($idEleccion, $idFrente)
    {
        // Crear una nueva instancia del modelo EleccionesFrente y asignar los valores
        $relacion = new EleccionesFrente();
        $relacion->COD_ELECCION = $idEleccion;
        $relacion->COD_FRENTE = $idFrente;

        // Guardar los datos en la tabla elecciones_frente
        $relacion->save();

        return response()->json(['message' => 'Relación guardada exitosamente']);
    }


    public function obtenerFrentesAsignados($idEleccion)
    {
        // Buscar los frentes asignados a la elección proporcionada
        $frentesAsignados = EleccionesFrente::where('COD_ELECCION', $idEleccion)
            ->select('COD_FRENTE')
            ->get();

        // Devolver los frentes asignados a esa elección
        return response()->json($frentesAsignados);
    }

    public function obtenerFrentesPorEleccion($idEleccion)
{
    try {
        $frentes = DB::table('elecciones_frente')
            ->join('frente', 'elecciones_frente.COD_FRENTE', '=', 'frente.COD_FRENTE')
            ->where('elecciones_frente.COD_ELECCION', $idEleccion)
            ->select('frente.NOMBRE_FRENTE')
            ->get();

        return response()->json(['frentes' => $frentes]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al obtener los frentes'],500);
}
}

    public function actualizarFrentes(Request $request)
    {
        $data = $request->all();

        try {
            // Elimina todos los frentes asociados a la elección recibida
            EleccionesFrente::where('COD_ELECCION', $data[0]['COD_ELECCION'])->delete();

            // Inserta los nuevos datos
            EleccionesFrente::insert($data);

            return response()->json(['message' => 'Frentes actualizados correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar los frentes'], 500);
        }
    }
    
    // Otros métodos como update, destroy, etc., según lo que necesites.
}
