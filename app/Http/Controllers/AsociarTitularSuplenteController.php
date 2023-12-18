<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Poblacion;
use App\Models\Eleccion;
use App\Models\AsociarTitularSuplente;
use Illuminate\Support\Facades\DB;

use App\Models\Elecciones;
use App\Notifications\NotificacionModelo;
use Illuminate\Support\Facades\Notification;

class AsociarTitularSuplenteController extends Controller
{
    public function store(Request $request)
    {
        // Valida y guarda los datos en la tabla asociar_titularSuplente
        $data = $request->validate([
            'ID_TS' => 'required',
            'COD_SIS' => 'required',
            'COD_COMITE' => 'required',
            'COD_TITULAR_SUPLENTE' => 'required',
        ]);

        AsociarTitularSuplente::create($data);

        return response()->json(['message' => 'Registro creado con éxito'], 201);
    }

    public function verListaComite($idComite)
    {
        // Filtrar registros con codTitular_Suplente = 1
        $titulares = DB::table('asociartitularsuplente')
            ->join('poblacion', 'asociartitularsuplente.COD_SIS', '=', 'poblacion.CODSIS')
            ->select(
                'poblacion.CARNETIDENTIDAD',
                'poblacion.NOMBRE',
                'poblacion.APELLIDO',
                'poblacion.ESTUDIANTE', // Agregar campo ESTUDIANTE
                'poblacion.DOCENTE' // Agregar campo DOCENTE
            )
            ->where('asociartitularsuplente.COD_COMITE', $idComite)
            ->where('asociartitularsuplente.COD_TITULAR_SUPLENTE', "1")
            ->get();

        // Consulta para los suplentes (codTitular_Suplente = 2)
        $suplentes = DB::table('asociartitularsuplente')
            ->join('poblacion', 'asociartitularsuplente.COD_SIS', '=', 'poblacion.CODSIS')
            ->select(
                'poblacion.CARNETIDENTIDAD',
                'poblacion.NOMBRE',
                'poblacion.APELLIDO',
                'poblacion.ESTUDIANTE', // Agregar campo ESTUDIANTE
                'poblacion.DOCENTE' // Agregar campo DOCENTE
            )
            ->where('asociartitularsuplente.COD_COMITE', $idComite)
            ->where('asociartitularsuplente.COD_TITULAR_SUPLENTE', "2")
            ->get();

        // Devuelve una respuesta JSON con los datos
        return response()->json(['titulares' => $titulares, 'suplentes' => $suplentes]);
    }


    public function verificarExistenciaComite($codComite)
{
    // Realiza una consulta para verificar la existencia del comité en la tabla asocialtitularsuplente
    $existeComite = DB::table('asociartitularsuplente')
        ->where('COD_COMITE', $codComite)
        ->exists();

    return response()->json(['existeComite' => $existeComite]);
}

public function verListaComiteConID($idComite)
{
    // Obtener información de titulares
    $titulares = DB::table('asociartitularsuplente')
        ->join('poblacion', 'asociartitularsuplente.COD_SIS', '=', 'poblacion.CODSIS')
        ->select(
            'asociartitularsuplente.COD_COMITE',
            'asociartitularsuplente.ID_TS',
            'asociartitularsuplente.COD_SIS',
            'poblacion.CARNETIDENTIDAD',
            'poblacion.NOMBRE',
            'poblacion.APELLIDO',
            'poblacion.ESTUDIANTE',
            'poblacion.DOCENTE'
        )
        ->where('asociartitularsuplente.COD_COMITE', $idComite)
        ->where('asociartitularsuplente.COD_TITULAR_SUPLENTE', "1")
        ->get();

    // Obtener información de suplentes
    $suplentes = DB::table('asociartitularsuplente')
        ->join('poblacion', 'asociartitularsuplente.COD_SIS', '=', 'poblacion.CODSIS')
        ->select(
            'asociartitularsuplente.COD_COMITE',
            'asociartitularsuplente.ID_TS',
            'asociartitularsuplente.COD_SIS',
            'poblacion.CARNETIDENTIDAD',
            'poblacion.NOMBRE',
            'poblacion.APELLIDO',
            'poblacion.ESTUDIANTE',
            'poblacion.DOCENTE'
        )
        ->where('asociartitularsuplente.COD_COMITE', $idComite)
        ->where('asociartitularsuplente.COD_TITULAR_SUPLENTE', "2")
        ->get();

    // Devuelve una respuesta JSON con los datos
    return response()->json(['titulares' => $titulares, 'suplentes' => $suplentes]);
}

//Envio de correos
public function enviarNotificacion($codComite)
    {
        $eleccion = Elecciones::where('COD_COMITE', $codComite)->first();

        // Obtener titulares y suplentes
        $titulares = $eleccion->titularesSuplentes()->where('COD_TITULAR_SUPLENTE', '1')->get();
        $suplentes = $eleccion->titularesSuplentes()->where('COD_TITULAR_SUPLENTE', '2')->get();

        foreach ($titulares as $titular) {
            $this->enviarMensajeMiembroComite($titular->poblacion, 'Titular', $codComite, $eleccion);
        }

        foreach ($suplentes as $suplente) {
            $this->enviarMensajeMiembroComite($suplente->poblacion, 'Suplente', $codComite, $eleccion);
        }

        return response()->json(['message' => 'Se ha notificado a los miembros del comité electoral.']);
    }

    private function enviarMensajeMiembroComite($miembro, $cargo, $codComite, $eleccion)
    {
        $mensaje = "TRIBUNAL ELECTORAL UNIVERSITARIO informa: \n"
            . "Usted ha sido elegido como miembro de comité electoral\n"
            . "Como $cargo. \n"
            . "Para el proceso electoral con motivo de la elección de: {$eleccion->MOTIVO_ELECCION}. \n"
            . "Que se llevará a cabo el día: {$eleccion->FECHA_ELECCION}.";

        Notification::send($miembro, new NotificacionModelo($mensaje));
}


////
public function actualizarDatos(Request $request)
{


    //return response()->json(['message' => 'ENTRA EN ACTUALIZARDATOS ']);


    $codComiteActual = $request->input('cod_comite_actual');
    $codSisActual = $request->input('cod_sis_actual');


    $permiso = DB::table('permisos')
    ->where('cod_sis', $codSisActual)
    ->where('cod_comite', $codComiteActual)
    ->first();

if ($permiso == null) {

    abort(403, 'No tienes permiso para acceder a esta área.');
}

    $tipo_usuario = $permiso->tipo_usuario;
    $codSisActual = $permiso->cod_sis;



    $poblacion = DB::table('poblacion')
    ->inRandomOrder()
    ->where('estudiante', $tipo_usuario == 'estudiante' ? 1 : 0)
    ->where('docente', $tipo_usuario == 'docente' ? 1 : 0)
    ->first();




    $codSisRandom = $poblacion->CODSIS;
    //return response()->json(['existeComite' => $codSisRandom]);


    $existeAsignacion = DB::table('asociartitularsuplente')
    ->where('COD_SIS', $codSisRandom)
    ->exists();

    if ($existeAsignacion) {
        return response()->json(['error' => 'El codComiteActual ya está asignado en la tabla asociartitularsuplente'], 400);
    }
        // Eliminar el registro actual de poblacion
    // Eliminar el registro actual de poblacion
    DB::table('poblacion')
    ->where('codcomite', $codComiteActual)
    ->where('codsis', $codSisActual)
    ->update(['codcomite' => null]);

    // Actualizar el campo codcomite en la tabla poblacion
    DB::table('poblacion')
    ->where('codsis', $codSisRandom)
    ->update(['codcomite' => $codComiteActual]);


    // Actualizar la tabla asociartitularsuplente
    DB::table('asociartitularsuplente')
        ->where('COD_COMITE', $codComiteActual)
        ->where('COD_SIS', $codSisActual)
        ->update(['COD_SIS' => $codSisRandom]);

    return response()->json(['message' => 'Datos actualizados correctamente']);

}


}

