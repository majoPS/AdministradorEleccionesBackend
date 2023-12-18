<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PoblacionFacuCarr;
use App\Models\Poblacion;


use Illuminate\Support\Facades\DB;


class PoblacionFacuCarrController extends Controller
{
    public function contarAlumnosPorFacultad($codFacultad)
    {
        $alumnosFacultad = PoblacionFacuCarr::where('cod_facultad', $codFacultad)->count();
        return response()->json(['alumnos_facultad' => $alumnosFacultad]);
    }

    public function contarAlumnosPorCarrera($codFacultad, $codCarrera)
    {
        $alumnosCarrera = PoblacionFacuCarr::where('cod_facultad', $codFacultad)
            ->where('cod_carrera', $codCarrera)
            ->count();
        return response()->json(['alumnos_carrera' => $alumnosCarrera]);
    }


  
    public function obtenerCantidadPorFacultad($facultad_id)
    {
        $cantidadDocentes = Poblacion::where('DOCENTE', 1)
            ->whereIn('codsis', function ($query) use ($facultad_id) {
                $query->select('codsis')
                    ->from('poblacion_facu_carr')
                    ->where('cod_facultad', $facultad_id);
            })
            ->count();
    
        $cantidadEstudiantes = Poblacion::where('ESTUDIANTE', 1)
            ->whereIn('codsis', function ($query) use ($facultad_id) {
                $query->select('codsis')
                    ->from('poblacion_facu_carr')
                    ->where('cod_facultad', $facultad_id);
            })
            ->count();
    
        return response()->json([
            'cantidad_docentes' => $cantidadDocentes,
            'cantidad_estudiantes' => $cantidadEstudiantes,
        ]);
    }
    

}
