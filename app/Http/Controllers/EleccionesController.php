<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Elecciones;
use App\Models\Frente;

use App\Models\EleccionesFacCarr;
use App\Models\EleccionesFrente;
use App\Models\Facultad;
use App\Models\Carrera;

use App\Notifications\NotificacionModelo;
use App\Models\Poblacion;
use Illuminate\Support\Facades\Notification;

use Illuminate\Support\Facades\DB;


class EleccionesController extends Controller
{
    public function index()
    {
        return Elecciones::get();
    }

    public function store(Request $request)
    {
        $cod_admin = $request->input('COD_ADMIN');
        $cod_frente = $request->input('COD_FRENTE');
        $cod_teu = $request->input('COD_TEU');
        $motivo_eleccion = $request->input('MOTIVO_ELECCION');
        $fecha_eleccion = $request->input('FECHA_ELECCION');
        $fecha_ini_convocatoria = $request->input('FECHA_INI_CONVOCATORIA');
        $fecha_fin_convocatoria = $request->input('FECHA_FIN_CONVOCATORIA');
        $eleccion_activa = $request->input('ELECCION_ACTIVA');
        $tipo_eleccion = $request->input('TIPO_ELECCION');
        $cod_facultad = $request->input('cod_facultad');
        $cod_carrera = $request->input('cod_carrera');

        $eleccion = new Elecciones();
        $eleccion->cod_admin = $cod_admin;
        $eleccion->cod_frente = $cod_frente;
        $eleccion->cod_teu = $cod_teu;
        $eleccion->cod_comite = rand(1, 500); // Genera el código de comité
        $eleccion->motivo_eleccion = $motivo_eleccion;
        $eleccion->tipo_eleccion = $tipo_eleccion;
        $eleccion->fecha_eleccion = $fecha_eleccion;
        $eleccion->fecha_ini_convocatoria = $fecha_ini_convocatoria;
        $eleccion->fecha_fin_convocatoria = $fecha_fin_convocatoria;
        $eleccion->eleccion_activa = $eleccion_activa;
        $eleccion->save();
        $cod_eleccion = $eleccion->getKey();

        if ($motivo_eleccion === 'universitaria') {
            // Obtener todas las facultades y sus carreras
            $facultadesYCarreras = DB::table('facultad')
                ->join('carrera', 'facultad.COD_FACULTAD', '=', 'carrera.COD_FACULTAD')
                ->select('facultad.COD_FACULTAD as cod_facultad', 'carrera.COD_CARRERA as cod_carrera')
                ->get();

            // Insertar en la tabla EleccionesFacCarr
            foreach ($facultadesYCarreras as $row) {
                DB::table('elecciones_fac_carr')->insert([
                    'COD_ELECCION' => $cod_eleccion,
                    'COD_FACULTAD' => $row->cod_facultad,
                    'COD_CARRERA' => $row->cod_carrera,
                ]);
            }
        } elseif ($motivo_eleccion === 'facultativa') {
            // Obtener las carreras de una facultad específica
            $carrerasFacultad = DB::table('carrera')
                ->where('COD_FACULTAD', $cod_facultad)
                ->select('COD_CARRERA as cod_carrera')
                ->get();

            // Insertar en la tabla EleccionesFacCarr
            foreach ($carrerasFacultad as $row) {
                DB::table('elecciones_fac_carr')->insert([
                    'COD_ELECCION' => $cod_eleccion,
                    'COD_FACULTAD' => $cod_facultad,
                    'COD_CARRERA' => $row->cod_carrera,
                ]);
            }
        } elseif ($motivo_eleccion === 'carrera') {
            // Insertar en la tabla EleccionesFacCarr solo para una carrera específica
            DB::table('elecciones_fac_carr')->insert([
                'COD_ELECCION' => $cod_eleccion,
                'COD_FACULTAD' => $cod_facultad,
                'COD_CARRERA' => $cod_carrera,
            ]);
        } elseif ($motivo_eleccion === 'universitaria2') {
            $eleccionFacCarr = new EleccionesFacCarr();
            $eleccionFacCarr->COD_ELECCION = $cod_eleccion;
            $eleccionFacCarr->COD_FACULTAD = $cod_facultad;
            $eleccionFacCarr->COD_CARRERA = $cod_carrera;
            $eleccionFacCarr->save();
        }

        $poblacion = Poblacion::all();

         if(!$poblacion->isEmpty()) {
            $mensaje = "TRIBUNAL ELECTORAL UNIVERSITARIO informa: \n"
                     . "Nuevo proceso electoral con motivo de elección de: $motivo_eleccion. \n"
                    . "Tipo de proceso electoral: $tipo_eleccion. \n"
                    . "Que se llevará a cabo la fecha: $fecha_eleccion. \n"
                    . "Con motivo de la elección de: $motivo_eleccion. \n"
                     . "La convocatoria está abierta a partir de $fecha_ini_convocatoria \n"
                     . "y finaliza en $fecha_fin_convocatoria.";
            $this->envioMasivoMensaje($mensaje);
        }

        return "La elección se ha creado correctamente.";
}

    public function obtenerEleccionPorId($id)
    {
        $eleccion = Elecciones::find($id);

        if (!$eleccion) {
            return response()->json(['error' => 'El proceso electoral no se encontró.'], 404);
        }

        return response()->json($eleccion);
    }

    public function update(Request $request, $id)
    {
        $eleccion = Elecciones::find($id);

        if (!$eleccion) {
            return response()->json(['error' => 'El proceso electoral no se encontró.'], 404);
        }

        $eleccion->update([
            'MOTIVO_ELECCION' => $request->MOTIVO_ELECCION,
            'FECHA_INI_CONVOCATORIA' => $request->FECHA_INI_CONVOCATORIA,
            'FECHA_FIN_CONVOCATORIA' => $request->FECHA_FIN_CONVOCATORIA,
            'FECHA_ELECCION' => $request->FECHA_ELECCION,
        ]);

        return response()->json(['message' => 'Proceso electoral actualizado correctamente.']);
    }

    public function asignarFrente(Request $request)
    {
        $eleccionId = $request->COD_ELECCION;
        $frenteId = $request->COD_FRENTE;

        $eleccion = Elecciones::find($eleccionId);
        $frente = Frente::find($frenteId);

        if (!$eleccion || !$frente) {
            return response()->json(['error' => 'El proceso electoral o el frente político no existen.'], 400);
        }

        $eleccion->frente()->associate($frente);
        $eleccion->save();

        return response()->json(['message' => 'Frente asignado al proceso electoral correctamente.']);
    }
    //envio de emails
    public function envioMasivoMensaje($mensaje)
    {
        $poblacion = Poblacion::all();
        Notification::send($poblacion, new NotificacionModelo($mensaje));
        
        return response()->json(['message' => 'Mensajes enviados exitosamente']);
}
}
