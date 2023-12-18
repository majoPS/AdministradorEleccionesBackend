<?php

namespace App\Http\Controllers;

use App\Models\Frente;
use App\Models\Elecciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Models\MotivoEliminacion;
use App\Http\Controllers\EleccionesFrenteController;

use Illuminate\Support\Facades\DB;

class FrenteController extends Controller
{
    public function index()
    {
        $frentes = Frente::where('ARCHIVADO', false)->get();

        return response()->json($frentes);
    }
    public function store(Request $request)
    {
        // Valida los datos recibidos en la solicitud

        // Obtiene el archivo de imagen y genera un nombre único para el logo
        $logo = $request->file('LOGO');
        $nombreLogo = "null";

        try {

            $nombreFrenteSolicitado = $request->input('NOMBRE_FRENTE');

           
            $codEleccionSolicitado = $request->input('COD_ELECCION');

            $existeFrente = Frente::where('NOMBRE_FRENTE', $nombreFrenteSolicitado)
                ->where('COD_ELECCION', $codEleccionSolicitado)
                ->exists();

            if ($existeFrente) {
                return response()->json(['error' => 'El frente ya está registrado para esta elección.'], 400);
            }
            // Intenta crear y guardar el frente político
            $frente = new Frente();
            $frente->NOMBRE_FRENTE = $request->NOMBRE_FRENTE;
            $frente->SIGLA_FRENTE = $request->SIGLA_FRENTE;
            $frente->COD_ELECCION = $request->COD_ELECCION;
            $frente->FECHA_INSCRIPCION = now();
            $frente->LOGO = $nombreLogo;
            $frente->save();

            // Obtiene el ID del frente político recién creado
            $idFrente = $frente->getKey();
            // Llama a la función para guardar la relación con elecciones
            $eleccionesFrenteController = new EleccionesFrenteController();
            $eleccionesFrenteController->guardarRelacionEleccionesFrente($request->COD_ELECCION, $idFrente);

            return response()->json(['message' => 'Se ha inscrito el frente correctamente.']);
        } catch (\Exception $e) {
            // Maneja cualquier error que pueda ocurrir durante el proceso
            return response()->json(['error' => 'Error al inscribir el frente político.', 'details' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $frente = Frente::where('ARCHIVADO',false)->find($id);

        if(!$frente)
        {
            return response()->json(['error' => 'No se encontró el frente.', 404]);
        }

        return response()->json($frente);
    }

    /*public function update(Request $request, $id)
    {
        $eleccionActiva = Elecciones::where('ELECCION_ACTIVA', true)->first();

        if (!$eleccionActiva) {
            return response()->json(['error' => 'No existe ninguna elección activa en este momento.'], 400);
        }

        $fechaIniConvocatoria = Carbon::parse($eleccionActiva->FECHA_INI_CONVOCATORIA);
        $fechaFinConvocatoria = Carbon::parse($eleccionActiva->FECHA_FIN_CONVOCATORIA);

        $fechaActual = now();
        if (!$fechaActual->between($fechaIniConvocatoria, $fechaFinConvocatoria)) {
            return response()->json(['error' => 'El periodo de inscripción de frentes no está activo.'], 400);
        }

        $request->validate([
            'NOMBRE_FRENTE' => 'required|string|min:2|max:30|unique:frente,NOMBRE_FRENTE,' . $id,
            'SIGLA_FRENTE' => 'required|string|min:2|max:15|unique:frente,SIGLA_FRENTE,' . $id,
            'LOGO' => 'image|mimes:jpeg,png,jpg|max:2048',
            'COD_CARRERA' => 'required'
        ]);

        $frente = Frente::find($id);

        if (!$frente) {
            return response()->json(['error' => 'No se encontró el frente para actualizar.'], 404);
        }

        $frente->NOMBRE_FRENTE = $request->NOMBRE_FRENTE;
        $frente->SIGLA_FRENTE = $request->SIGLA_FRENTE;
        $frente->COD_CARRERA = $request->COD_CARRERA;

        if ($request->hasFile('LOGO')) {
            // Elimina el logo anterior
            Storage::delete('public/logos/' . $frente->LOGO);

            // Sube y guarda el nuevo logo
            $logo = $request->file('LOGO');
            $nombreLogo = uniqid() . '-' . $logo->getClientOriginalName();
            $logo->storeAs('public/logos', $nombreLogo);

            $frente->LOGO = $nombreLogo;
        }

        $frente->save();

        return response()->json(['message' => 'Frente actualizado correctamente.']);
    }*/



    public function update(Request $request, $id)
    {
        $request->validate([
            'NOMBRE_FRENTE' => 'required|string|min:2|max:30',
            'SIGLA_FRENTE' => 'required|string|min:2|max:15',
            //'LOGO' => 'image|mimes:jpeg,png,jpg|max:2048',
            'COD_CARRERA' => 'required',
        ]);

        $frente = Frente::find($id);

        if(!$frente)
        {
            return response()->json(['error' => 'No se encontró el frente.']);
        }

        $frente -> NOMBRE_FRENTE = $request->input('NOMBRE_FRENTE');
        $frente -> SIGLA_FRENTE = $request->input('SIGLA_FRENTE');
        $frente -> COD_CARRERA = $request->input('COD_CARRERA');

        /*if ($request->hasFile('LOGO')) {
            // Elimina el logo anterior
            Storage::delete('public/logos/' . $frente->LOGO);

            // Sube y guarda el nuevo logo
            $logo = $request->file('LOGO');
            $nombreLogo = uniqid() . '-' . $logo->getClientOriginalName();
            $logo->storeAs('public/logos', $nombreLogo);

            $frente->LOGO = $nombreLogo;
        }*/

        $frente -> save();
        return response()->json(['message' => 'Frente actualizado correctamente']);
    }


    public function delete(Request $request, $id)
    {
        $motivo = $request->MOTIVO;

        if(empty(trim($motivo))){
            return response()->json(['error' => 'El motivo no puede estar vacio.']);
        }

        $motivoEliminacion = MotivoEliminacion::where('MOTIVO', $motivo)->first();

        if(!$motivoEliminacion){
            $motivoEliminacion = MotivoEliminacion::create(['MOTIVO' => $motivo]);
        }

        $frente = Frente::where('ARCHIVADO',false)->find($id);

        if(!$frente)
        {
            return response()->json(['error' => 'No se encontró el frente.']);
        }

        $frente -> ARCHIVADO = true;
        $frente -> COD_MOTIVO = $motivoEliminacion->COD_MOTIVO;
        $frente -> save();

        return response()->json(['message' => 'El frente se ha eliminado correctamente.']);
    }

    public function listarFrentesYCandidatos()
    {

        $frentes = Frente::with(['candidato', 'candidato.CARGO_POSTULADO'])->get();

        return response()->json(['frentes' => $frentes]);
    }

    public function obtenerFrentesPorCarrera($COD_CARRERA)
    {

        if (!is_numeric($COD_CARRERA)) {
            return response()->json(['error' => 'El código de carrera debe ser un número.'], 400);
        }

        $frentesCarrera = Frente::where('COD_CARRERA', $COD_CARRERA)->get();

        if ($frentesCarrera->isEmpty()) {
            return response()->json(['mensaje' => 'No se encontraron frentes para la carrera especificada.']);
        }

        return response()->json(['frentes' => $frentesCarrera]);
    }


    public function getFrentesByEleccion($cod_eleccion)
    {
        // Obtener los frentes que no están asociados a la elección y los asociados a la frente seleccionada
        $frentesDisponibles = DB::table('frente')
            ->where('ARCHIVADO', false)
            ->where(function ($query) use ($cod_eleccion) {
                $query
                    ->whereNotIn('COD_FRENTE', function ($subquery) use ($cod_eleccion) {
                        $subquery->select('COD_FRENTE')
                            ->from('elecciones_frente')
                            ->where('COD_ELECCION', '<>', $cod_eleccion);
                    })
                    ->orWhere('COD_FRENTE', '=', $cod_eleccion);
            })
            ->select(
                'COD_FRENTE',
                'NOMBRE_FRENTE',
                'SIGLA_FRENTE',
                'FECHA_INSCRIPCION',
                'LOGO',
                'COD_MOTIVO',
                'COD_CARRERA',
                'COD_ELECCION'
            )
            ->get();

        return response()->json($frentesDisponibles);
    }


}
