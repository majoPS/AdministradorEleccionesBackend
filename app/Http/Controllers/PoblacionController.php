<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poblacion;
use App\Models\AsociarTitularSuplente;
use App\Models\Elecciones;
use App\Models\EleccionesFacCarr;
use Illuminate\Support\Facades\DB;

class PoblacionController extends Controller
{

    public function index()
    {
        // Obtiene todos los registros de la tabla eleccions
        $elecciones = Poblacion::all();

        // Devuelve los datos como respuesta JSON
        return response()->json(['data' => $elecciones]);
    }


    public function asignarVocales2($COD_COMITE, $COD_ELECCION) {
        // Filtrar 6 docentes y 4 estudiantes de forma aleatoria en base al COD_COMITE

        $motivo = Elecciones::where('cod_eleccion', $COD_ELECCION)->value('motivo_eleccion');

        $facultad_id = EleccionesFacCarr::where('cod_eleccion', $COD_ELECCION)->value('cod_facultad');

        $docentes = Poblacion::where('DOCENTE', 1)
        ->whereIn('codsis', function ($query) use ($facultad_id) {
            $query->select('codsis')
                ->from('poblacion_facu_carr')
                ->where('cod_facultad', $facultad_id);
        })
        ->where('CODCOMITE', $COD_COMITE)
        ->inRandomOrder()
        ->limit(6)
        ->get();

    $estudiantes = Poblacion::where('ESTUDIANTE', 1)
        ->whereIn('codsis', function ($query) use ($facultad_id) {
            $query->select('codsis')
                ->from('poblacion_facu_carr')
                ->where('cod_facultad', $facultad_id);
        })
        ->where('CODCOMITE', $COD_COMITE)
        ->inRandomOrder()
        ->limit(4)
        ->get();



        return response()->json(['data' => $docentes]);
        // Puedes agregar lógica adicional si es necesario

        return response()->json(['message' => 'Datos registrados en la tabla asociar_titularSuplente']);
    }

//ENTRA SEGUNDO AQUI PARA ASIGNAR EN POBLACION CODCOMITE
    public function asignarVocales($COD_COMITE) {
        // Filtrar 6 docentes y 4 estudiantes de forma aleatoria en base al COD_COMITE
        $docentes = Poblacion::inRandomOrder()
            ->where('DOCENTE', 1)
            ->where('CODCOMITE', $COD_COMITE)
            ->limit(6)
            ->get();

        $estudiantes = Poblacion::inRandomOrder()
            ->where('ESTUDIANTE', 1)
            ->where('CODCOMITE', $COD_COMITE)
            ->limit(4)
            ->get();



        // Dividir los docentes y estudiantes en dos arrays (3 docentes y 2 estudiantes en cada uno)
        $array1 = $docentes->take(3)->concat($estudiantes->take(2));
        $array2 = $docentes->skip(3)->concat($estudiantes->skip(2));



        // Asignar codTitular_Suplente = 1 a los elementos de $array1
        foreach ($array1 as $element) {
            AsociarTitularSuplente::create([
                'COD_SIS' => $element->CODSIS,
                'COD_COMITE' => $COD_COMITE,
                'COD_TITULAR_SUPLENTE' => "1"
            ]);

            //return response()->json($element);
        }

        // Asignar codTitular_Suplente = 2 a los elementos de $array2
        foreach ($array2 as $element) {
            AsociarTitularSuplente::create([
                'COD_SIS' => $element->CODSIS,
                'COD_COMITE' => $COD_COMITE,
                'COD_TITULAR_SUPLENTE' => "2"
            ]);
        }



        // Puedes agregar lógica adicional si es necesario

        return response()->json(['message' => 'Datos registrados en la tabla asociar_titularSuplente']);
    }
}
