<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facultad;

use App\Models\Carrera;
use App\Models\EleccionesFacCarr;

use Illuminate\Support\Facades\DB;


class FacultadController extends Controller
{
    public function index()
    {
        $facultades = Facultad::all();
        return response()->json($facultades);
    }


 
public function obtenerFacultadesPorEleccion($codEleccion)
{
    $facultades = EleccionesFacCarr::select('COD_FACULTAD')
        ->where('COD_ELECCION', $codEleccion)
        ->distinct()
        ->get();

    $facultades = Facultad::whereIn('COD_FACULTAD', $facultades)->get();

    return response()->json($facultades);
}

public function contarAlumnosPorFacultad($codFacultad) {
    $cantidadAlumnos = DB::table('poblacion_facu_carr')
        ->where('cod_facultad', $codFacultad)
        ->where('estudiante', 1) // Suponiendo que 'estudiante' es una columna que representa si es estudiante
        ->count();

    return $cantidadAlumnos;
}

public function contarAlumnosPorCarrera($codFacultad, $codCarrera) {
    $cantidadAlumnos = DB::table('poblacion_facu_carr')
        ->where('cod_facultad', $codFacultad)
        ->where('cod_carrera', $codCarrera)
        ->where('estudiante', 1) // Considerando 'estudiante' como una columna que define si es estudiante
        ->count();

    return $cantidadAlumnos;
}

  


    // Otros métodos del controlador (store, show, update, delete) según tus necesidades
}
