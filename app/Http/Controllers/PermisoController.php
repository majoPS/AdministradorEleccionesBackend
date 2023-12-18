<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poblacion;
use App\Models\AsociarTitularSuplente;
use App\Models\Permiso;
use App\Models\Elecciones;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Asegúrate de importar Carbon al inicio del archivo


class PermisoController extends Controller
{
    public function index()
    {
        // Obtener todos los permisos
        $permisos = Permiso::all();
        return response()->json($permisos);
    }


    // PoblacionController.php



    public function agregarPermiso(Request $request)
    {
        try {
            // Validación de datos
            $request->validate([
                'cod_sis' => 'required',
                'cod_comite' => 'required',
                'motivo' => 'required',
                // ... (otras reglas de validación según tus necesidades)
            ]);

            // Obtener la fecha actual
            $fechaActual = now();

            // Calcular la fecha de fin de solicitud (24 horas después)
            $fechaFinSolicitud = Carbon::parse($fechaActual)->addDay();

            // Obtener el tipo de usuario (estudiante o docente) desde la tabla poblacion
            $tipoUsuario = Poblacion::where('codsis', $request->input('cod_sis'))
                ->where('codcomite', $request->input('cod_comite'))
                ->value(DB::raw('CASE WHEN estudiante = 1 THEN "estudiante" WHEN docente = 1 THEN "docente" ELSE null END'));

            // Validar que se obtenga un tipo de usuario válido
            if (!$tipoUsuario) {
                return response()->json(['error' => 'Tipo de usuario no válido en la tabla poblacion'], 500);
            }

            // Crear nuevo permiso
            $permiso = Permiso::create([
                'cod_sis' => $request->input('cod_sis'),
                'motivo' => $request->input('motivo'),
                'cod_comite' => $request->input('cod_comite'),
                'comprobante_entregado' => $request->input('comprobante_entregado'),
                'fecha_solicitud' => $fechaActual, // Fecha de la elección
                'fecha_fin_solicitud' => $fechaFinSolicitud, // 24 horas después
                'fecha_permiso' => now(), // Fecha actual
                'estado' => 'entregado_con_retraso',
                'codsis_sustituto' => null,
                'fecha_comprobante_entregado' => null,
                'tipo_usuario' => $tipoUsuario, // Agregar el tipo de usuario
                // ... (otros campos según tus necesidades)
            ]);

            return response()->json(['message' => 'Permiso agregado correctamente', 'data' => $permiso], 201);
        } catch (\Exception $e) {
            // Manejar cualquier excepción y devolver una respuesta 500 con un mensaje descriptivo
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



public function procesarComprobanteEntregado(Request $request)
{
    // Validación de datos
    $request->validate([
        'comprobante_entregado' => 'required|boolean',
        // ... (otras reglas de validación según tus necesidades)
    ]);

    // Obtener el permiso asociado al cod_sis y cod_comite
    $permiso = Permiso::where('cod_sis', $request->input('cod_sis'))
        ->where('cod_comite', $request->input('cod_comite'))
        ->first();

    // Validar que se encuentre el permiso
    if (!$permiso) {
        return response()->json(['error' => 'No se encontró el permiso asociado'], 404);
    }

    // Actualizar fecha_comprobante_entregado con la fecha actual si comprobante_entregado es true
    if ($request->input('comprobante_entregado')) {
        $permiso->fecha_comprobante_entregado = now();
        $permiso->comprobante_entregado = $request->input('comprobante_entregado');
        $permiso->save();

        // Calcular si fecha_comprobante_entregado está en el rango entre fecha_solicitud y fecha_fin_solicitud
        $fechaComprobanteEntregado = Carbon::parse($permiso->fecha_comprobante_entregado);
        if ($fechaComprobanteEntregado->between(
            Carbon::parse($permiso->fecha_solicitud),
            Carbon::parse($permiso->fecha_fin_solicitud)
        )) {
            // Actualizar el estado a 'entregado'
            $permiso->estado = 'entregado_a_tiempo';
            $permiso->save();
        }
    }

    // Devolver el resultado
    return response()->json(['message' => 'Procesamiento completado']);
}



public function obtenerEstadoComprobante($codSis, $codComite)
{
    // Buscar el permiso en la base de datos
    $permiso = Permiso::where('cod_sis', $codSis)
        ->where('cod_comite', $codComite)
        ->first();

    // Verificar si se encontró el permiso
    if ($permiso) {
        // Devolver el estado del comprobante
        return response()->json(['comprobante_entregado' => $permiso->comprobante_entregado]);
    }

    // Si no se encuentra el permiso, devolver un error 404
    return response()->json(['message' => 'Permiso no encontrado'], 404);
}


public function obtenerEstadoComprobanteAtiempo($codSis, $codComite)
{
    // Buscar el permiso en la base de datos
    $permiso = Permiso::where('cod_sis', $codSis)
        ->where('cod_comite', $codComite)
        ->first();

    // Verificar si se encontró el permiso
    if ($permiso) {
        // Devolver el estado del comprobante
        return response()->json(['estado' => $permiso->estado]);
    }

    // Si no se encuentra el permiso, devolver un error 404
    return response()->json(['message' => 'Permiso no encontrado'], 404);
}

// PermisoController.php

public function verificarPermiso($codSis, $codComite)
{
    // Verificar si existe un permiso para el vocal en la tabla permisos
    $tienePermiso = Permiso::where('cod_sis', $codSis)
        ->where('cod_comite', $codComite)
        ->exists();

    return response()->json(['tiene_permiso' => $tienePermiso]);
}



}
