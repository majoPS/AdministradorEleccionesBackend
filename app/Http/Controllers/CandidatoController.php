<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidato;
use App\Models\Frente;
use App\Models\Poblacion;

use Illuminate\Support\Facades\DB;

class CandidatoController extends Controller
{
    public function asignarCandidatoAFrente(Request $request)
    {
        $frenteId = $request->COD_FRENTE;
        $ci = $request->CARNETIDENTIDAD;
        $cargoPostulado = $request->CARGO_POSTULADO;


        $poblacion = Poblacion::where('CARNETIDENTIDAD', $ci)->first();

        if($poblacion)
        {
            $nuevoIdCandidato = mt_rand(10000, 99999);
            $nuevoCandidato = new Candidato;

            $nuevoCandidato->COD_CANDIDATO = $nuevoIdCandidato;
            $nuevoCandidato->COD_CARNETIDENTIDAD = $poblacion->CARNETIDENTIDAD;
            $nuevoCandidato->CARGO_POSTULADO = $cargoPostulado;
            $nuevoCandidato->HABILITADO = 1;
            $nuevoCandidato->COD_FRENTE = $frenteId;

            $nuevoCandidato->save();

            return response()->json(['success', 'Candidato asignado correctmanete.']);
        }

        return response()->json(['error', 'No se encontró el CI proporcionado.'], 400);
    }

    public function obtenerCandidatosPorFrente($codFrente)
    {
        $candidatos = Candidato::where('COD_FRENTE', $codFrente)->get();

        return response()->json($candidatos);
    }

    public function buscarCarnet($carnetIdentidad)
{
    $carnetExiste = Poblacion::where('carnetidentidad', $carnetIdentidad)->exists();

    return response()->json($carnetExiste);
}

public function verificarExistenciaCandidato(Request $request)
{
    $carnetIdentidad = $request->get('carnetIdentidad');

    $existeCandidato = Candidato::where('COD_CARNETIDENTIDAD', $carnetIdentidad)
        ->exists();

    return response()->json(['existeCandidato' => $existeCandidato]);
}

public function actualizarCandidato(Request $request)
{
    $candidato = Candidato::find($request->COD_CANDIDATO);

    if ($candidato) {
        $candidato->COD_CARNETIDENTIDAD = $request->CARNET_IDENTIDAD;
        $candidato->CARGO_POSTULADO = $request->CARGO;
        // Aquí puedes agregar más campos que se actualicen

        $candidato->save();

        return response()->json(['message' => 'Candidato actualizado correctamente']);
    } else {
        return response()->json(['error' => 'No se encontró el candidato'], 404);
    }
}


public function obtenerFrentesYCandidatos($idEleccion)
{
    // Obtener frentes para la elección específica con sus candidatos
    $frentes = DB::table('frente')
        ->join('elecciones_frente', 'frente.COD_FRENTE', '=', 'elecciones_frente.COD_FRENTE')
        ->where('elecciones_frente.COD_ELECCION', $idEleccion)
        ->select(
            'frente.COD_FRENTE',
            'frente.NOMBRE_FRENTE',
            'frente.SIGLA_FRENTE',
            'poblacion.NOMBRE as NOMBRE_CANDIDATO',
            'poblacion.APELLIDO as APELLIDO_CANDIDATO',
            'poblacion.CARNETIDENTIDAD'
        )
        ->leftJoin('candidato', 'candidato.COD_FRENTE', '=', 'frente.COD_FRENTE')
        ->leftJoin('poblacion', 'candidato.COD_CARNETIDENTIDAD', '=', 'poblacion.CARNETIDENTIDAD')
        ->get();

    // Organizar los frentes y candidatos en una estructura de datos más adecuada
    $frentesConCandidatos = [];
    foreach ($frentes as $frente) {
        $frenteId = $frente->COD_FRENTE;

        // Verificar si el frente ya existe en el arreglo
        if (!array_key_exists($frenteId, $frentesConCandidatos)) {
            $frentesConCandidatos[$frenteId] = [
                'COD_FRENTE' => $frenteId,
                'NOMBRE_FRENTE' => $frente->NOMBRE_FRENTE,
                'SIGLA_FRENTE' => $frente->SIGLA_FRENTE,
                'candidatos' => [],
            ];
        }

        // Agregar candidato al frente actual
        if ($frente->NOMBRE_CANDIDATO) {
            $frentesConCandidatos[$frenteId]['candidatos'][] = [
                'NOMBRE' => $frente->NOMBRE_CANDIDATO,
                'APELLIDO' => $frente->APELLIDO_CANDIDATO,
                'CARNETIDENTIDAD' => $frente->CARNETIDENTIDAD,
            ];
        }
    }

    return response()->json(['frentes' => array_values($frentesConCandidatos)]);
}



public function reasignarCandidato(Request $request)
    {
        $carnetNuevo = $request->input('carnetIdentidadNuevo');
        $carnetSustitucion = $request->input('carnetIdentidadAntiguo');

        // Buscar candidato existente por el carnetSustitucion
        $candidatoExistente = Candidato::where('COD_CARNETIDENTIDAD', $carnetSustitucion)->first();

        if (!$candidatoExistente) {
            return response()->json(['error' => 'Candidato not found'], 404);
        }

        // Buscar datos de la población por el carnetNuevo
        $poblacionNuevo = Poblacion::where('CARNETIDENTIDAD', $carnetNuevo)->first();

        if (!$poblacionNuevo) {
            return response()->json(['error' => 'Nuevo candidato not found in the poblacion table'], 404);
        }

        // Actualizar los campos del candidato existente con los datos del nuevo candidato
        //$candidatoExistente->NOMBRE = $poblacionNuevo->NOMBRE;
        //$candidatoExistente->APELLIDO = $poblacionNuevo->APELLIDO;
        $candidatoExistente->COD_CARNETIDENTIDAD = $poblacionNuevo->CARNETIDENTIDAD;

        $candidatoExistente->save();

        return response()->json(['success' => 'Candidato actualizado correctamente.']);
    }



}
