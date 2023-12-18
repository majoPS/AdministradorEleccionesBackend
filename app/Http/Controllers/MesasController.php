<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesas;
use App\Models\ActaAperturaMesa;
use App\Models\PoblacionFacuCarr;
use App\Models\EleccionesFacCarr;
use App\Models\Elecciones;


use App\Http\Controllers\ListaVotantesController;


use Dompdf\Dompdf;
use Dompdf\Options;


use Illuminate\Support\Facades\DB;



class MesasController extends Controller
{
    // Métodos del controlador, como store, index, show, update, destroy, etc.
    // por ejemplo, store para la creación de mesas:
    public function store(Request $request)
    {
        $mesa = Mesas::create($request->all());

        // Puedes realizar acciones adicionales aquí, si es necesario
        // Por ejemplo, devolver una respuesta JSON con la mesa creada
        return response()->json($mesa, 201);
    }


    public function asignarMesasPorCarrera($cod_eleccion)
{
    try {
        $carreras = EleccionesFacCarr::where('COD_ELECCION', $cod_eleccion)->get();

        // Asociar cada letra del alfabeto con un apellido específico
        $apellidosPorLetra = [
            'A' => 'ApellidoA',
            'B' => 'ApellidoB',
            'C' => 'ApellidoC',
            'D' => 'ApellidoD',
            'E' => 'ApellidoE',
            'F' => 'ApellidoF',
            'G' => 'ApellidoG',
            'H' => 'ApellidoH',
            'I' => 'ApellidoI',
            'J' => 'ApellidoJ',
            'K' => 'ApellidoK',
            'L' => 'ApellidoL',
            'M' => 'ApellidoM',
            'N' => 'ApellidoN',
            'O' => 'ApellidoO',
            'P' => 'ApellidoP',
            'Q' => 'ApellidoQ',
            'R' => 'ApellidoR',
            'S' => 'ApellidoS',
            'T' => 'ApellidoT',
            'U' => 'ApellidoU',
            'V' => 'ApellidoV',
            'W' => 'ApellidoW',
            'X' => 'ApellidoX',
            'Y' => 'ApellidoY',
            'Z' => 'ApellidoZ',
        ];

        foreach ($carreras as $carrera) {
            $alfabeto = range('A', 'Z');

            $cod_carrera = $carrera->COD_CARRERA;
            $cod_facultad = $carrera->COD_FACULTAD;

            $alumnosPorCarrera = PoblacionFacuCarr::where('cod_facultad', $cod_facultad)
                ->where('cod_carrera', $cod_carrera)
                ->count();

            $capacidadMesa = 50;
            $mesasAsignadas = ceil($alumnosPorCarrera / $capacidadMesa);
            $alfabetoGrupos = array_chunk($alfabeto, ceil(count($alfabeto) / $mesasAsignadas));

            for ($i = 0; $i < $mesasAsignadas; $i++) {
                $mesa = new Mesas();
                $mesa->COD_ELECCION = $cod_eleccion;
                $mesa->COD_FACULTAD = $cod_facultad;
                $mesa->COD_CARRERA = $cod_carrera;
                $mesa->NUM_MESA = $i + 1;
                $mesa->CANT_EST_MESA = 0; // Asignar la cantidad de estudiantes inicial, si es necesario

                // Obtener el apellido asociado a la primera y última letra del grupo
                $primerApellido = $apellidosPorLetra[$alfabetoGrupos[$i][0]] ?? 'SinApellido';
                $ultimaLetraGrupo = end($alfabetoGrupos[$i]);
                $ultimoApellido = $apellidosPorLetra[$ultimaLetraGrupo] ?? 'SinApellido';
                $mesa->APELLIDOS_ESTUDIANTES = $primerApellido . '-' . $ultimoApellido;

                $mesa->save();
            }
        }

        error_log('Mesas asignadas correctamente para la elección ' . $cod_eleccion);

        // Retrieve COD_MESA values after generating tables
        $cod_mesas = Mesas::where('COD_ELECCION', $cod_eleccion)->pluck('COD_MESA')->toArray();
        $fechaEleccion = Elecciones::where('COD_ELECCION', $cod_eleccion)->value('FECHA_ELECCION');


        // Insert COD_MESA values into the acta_apertura_mesas table
        foreach ($cod_mesas as $cod_mesa) {
            $actaApertura = new ActaAperturaMesa();
            $actaApertura->cod_mesa = $cod_mesa;
            $actaApertura->dia_instalacion_mesa = $fechaEleccion;
            // You may set other columns as needed
            $actaApertura->save();
        }

        $listaVotantesController = new ListaVotantesController();
        $listaVotantesController->generarListasVotantes($cod_eleccion);



        return response()->json(['message' => 'Mesas asignadas correctamente']);

    } catch (\Exception $e) {
        // Manejo de errores, puedes registrar el error o devolver un mensaje específico
        return response()->json(['error' => 'Error durante la asignación de mesas: ' . $e->getMessage()], 500);
    }


}


public function verificarExistenciaMesaPorEleccion($codEleccion)
{
    // Utiliza el modelo 'Mesa' para realizar la consulta
    $existeMesa = Mesa::where('COD_ELECCION', $codEleccion)->exists();

    return response()->json(['existeMesa' => $existeMesa]);
}


    public function asignarMesasPorCarrera0502($cod_eleccion)
{
    $carreras = EleccionesFacCarr::where('COD_ELECCION', $cod_eleccion)->get();

    // Asociar cada letra del alfabeto con un apellido específico
    $apellidosPorLetra = [
        'A' => 'ApellidoA',
        'B' => 'ApellidoB',
        'C' => 'ApellidoC',
        'D' => 'ApellidoD',
        'E' => 'ApellidoE',
        'F' => 'ApellidoF',
        'G' => 'ApellidoG',
        'H' => 'ApellidoH',
        'I' => 'ApellidoI',
        'J' => 'ApellidoJ',
        'K' => 'ApellidoK',
        'L' => 'ApellidoL',
        'M' => 'ApellidoM',
        'N' => 'ApellidoN',
        'O' => 'ApellidoO',
        'P' => 'ApellidoP',
        'Q' => 'ApellidoQ',
        'R' => 'ApellidoR',
        'S' => 'ApellidoS',
        'T' => 'ApellidoT',
        'U' => 'ApellidoU',
        'V' => 'ApellidoV',
        'W' => 'ApellidoW',
        'X' => 'ApellidoX',
        'Y' => 'ApellidoY',
        'Z' => 'ApellidoZ',
    ];


    foreach ($carreras as $carrera) {
        $alfabeto = range('A', 'Z');

        $cod_carrera = $carrera->COD_CARRERA;
        $cod_facultad = $carrera->COD_FACULTAD;
        $alumnosPorCarrera = PoblacionFacuCarr::where('cod_facultad', $cod_facultad)
            ->where('cod_carrera', $cod_carrera)
            ->count();

        $capacidadMesa = 50;
        $mesasAsignadas = ceil($alumnosPorCarrera / $capacidadMesa);
        $alfabetoGrupos = array_chunk($alfabeto, ceil(count($alfabeto) / $mesasAsignadas));

        for ($i = 0; $i < $mesasAsignadas; $i++) {
            $mesa = new Mesas();
            $mesa->COD_ELECCION = $cod_eleccion;
            $mesa->COD_FACULTAD = $cod_facultad;
            $mesa->COD_CARRERA = $cod_carrera;
            $mesa->NUM_MESA = $i + 1;
            $mesa->CANT_EST_MESA = 0; // Asignar la cantidad de estudiantes inicial, si es necesario

            // Obtener el apellido asociado a la primera letra del grupo
            $primeraLetraGrupo = $alfabetoGrupos[$i][0];
            $ultimaLetraGrupo = end($alfabetoGrupos[$i]);
             // Verificar si la letra existe en el array asociativo
    if (isset($apellidosPorLetra[$primeraLetraGrupo]) && isset($apellidosPorLetra[$ultimaLetraGrupo])) {
        $rangoApellidos = $apellidosPorLetra[$primeraLetraGrupo] . '-' . $apellidosPorLetra[$ultimaLetraGrupo];
        $mesa->APELLIDOS_ESTUDIANTES = $rangoApellidos;
    } else {
        // Manejar el caso si la letra no tiene un apellido asociado
        $mesa->APELLIDOS_ESTUDIANTES = 'SinApellido';
    }
            $mesa->save();
        }
    }
    return response()->json(['message' => 'Mesas asignadas correctamente']);
}



    //funciona para eleccion tipo facultad
    public function asignarMesasPorCarreraprueva2($cod_eleccion)
    {
        $carreras = EleccionesFacCarr::where('COD_ELECCION', $cod_eleccion)->get();

        foreach ($carreras as $carrera) {

            $alfabeto = range('A', 'Z');

            $cod_carrera = $carrera->COD_CARRERA;
            $cod_facultad = $carrera->COD_FACULTAD;
            $alumnosPorCarrera = PoblacionFacuCarr::where('cod_facultad', $cod_facultad)
                ->where('cod_carrera', $cod_carrera)
                ->count();

            $capacidadMesa = 50;
            $mesasAsignadas = ceil($alumnosPorCarrera / $capacidadMesa);
            $alfabetoGrupos = array_chunk($alfabeto, ceil(count($alfabeto) / $mesasAsignadas));

            for ($i = 0; $i < $mesasAsignadas; $i++) { // Cambiado a $i = 0
                $mesa = new Mesas();
                $mesa->COD_ELECCION = $cod_eleccion;
                $mesa->COD_FACULTAD = $cod_facultad;
                $mesa->COD_CARRERA = $cod_carrera;
                $mesa->NUM_MESA = $i + 1;
                $mesa->CANT_EST_MESA = 0; // Asignar la cantidad de estudiantes inicial, si es necesario
                $mesa->APELLIDOS_ESTUDIANTES = implode(",", $alfabetoGrupos[$i]);

                $mesa->save();
            }
        }
        return response()->json(['message' => 'Mesas asignadas correctamente']);
    }




    public function asignarMesasPorCarreraPrueva($cod_eleccion)
{
    $carreras = EleccionesFacCarr::where('COD_ELECCION', $cod_eleccion)->get();

    foreach ($carreras as $carrera) {
        $cod_carrera = $carrera->COD_CARRERA;
        $cod_facultad = $carrera->COD_FACULTAD;

        $alfabeto = range('A', 'Z'); // Obtener el alfabeto

        $capacidadMesa = 50;
        $mesasAsignadas = ceil(count($alfabeto) / $capacidadMesa);

        // Dividir el alfabeto en grupos según el número de mesas
        $alfabetoGrupos = array_chunk($alfabeto, ceil(count($alfabeto) / $mesasAsignadas));

        // Asignar cada grupo de apellidos a una mesa
        for ($i = 0; $i < $mesasAsignadas; $i++) {
            $mesa = new Mesas();
            $mesa->COD_ELECCION = $cod_eleccion;
            $mesa->COD_FACULTAD = $cod_facultad;
            $mesa->COD_CARRERA = $cod_carrera;
            $mesa->NUM_MESA = $i + 1;
            $mesa->CANT_EST_MESA = 0; // Asignar la cantidad de estudiantes inicial, si es necesario

            // Asignar el rango de apellidos al grupo de la mesa
            $mesa->APELLIDOS_ESTUDIANTES = implode(",", $alfabetoGrupos[$i]);

            $mesa->save();
        }

        return response()->json(['message' => 'Mesas asignadas correctamente']);
    }
}

    public function asignarMesasPorCarrera3($cod_eleccion)
    {
        // Obtener las carreras para la elección especificada
        $carreras = EleccionesFacCarr::where('COD_ELECCION', $cod_eleccion)->get();

        foreach ($carreras as $carrera) {
            $cod_carrera = $carrera->COD_CARRERA;
            $cod_facultad = $carrera->COD_FACULTAD;

            // Insertar 3 mesas por cada carrera
            for ($i = 1; $i <= 3; $i++) {
                $mesa = new Mesas();
                $mesa->COD_ELECCION = $cod_eleccion;
                $mesa->COD_FACULTAD = $cod_facultad;
                $mesa->COD_CARRERA = $cod_carrera;
                $mesa->NUM_MESA = $i;
                $mesa->CANT_EST_MESA = 0; // Asignar la cantidad de estudiantes inicial, si es necesario
                $mesa->save();
            }
        }

        return response()->json(['message' => 'Mesas asignadas por carrera para la elección.']);
    }


    public function listarMesasAsignadas()
    {



    $mesasAsignadas = Mesas::select(
        'Mesas.*',
        'Eleccioness.MOTIVO_ELECCION',
        'Facultad.nombre_facultad',
        'Carrera.nombre_carrera'
    )
        ->join('Eleccioness', 'Mesas.COD_ELECCION', '=', 'Eleccioness.COD_ELECCION')
        ->join('Facultad', 'Mesas.COD_FACULTAD', '=', 'Facultad.COD_FACULTAD')
        ->join('Carrera', 'Mesas.COD_CARRERA', '=', 'Carrera.COD_CARRERA')
        ->get();

        return response()->json([
            'listaElecciones' => Elecciones::all(),
            'mesasPorCarrera' => $mesasPorCarrera,
            'mesasDetalladas' => $mesasAsignadas
        ]);
}


public function listarMesasAsignadas2()
{
    $mesasAsignadas = Mesas::select(
        'Eleccioness.COD_ELECCION',
        'Eleccioness.MOTIVO_ELECCION',
        'Facultad.nombre_facultad',
        'Eleccioness.fecha_eleccion',
        'Carrera.COD_CARRERA',
        'Carrera.nombre_carrera'
    )
        ->join('Eleccioness', 'Mesas.COD_ELECCION', '=', 'Eleccioness.COD_ELECCION')
        ->join('Facultad', 'Mesas.COD_FACULTAD', '=', 'Facultad.COD_FACULTAD')
        ->join('Carrera', 'Mesas.COD_CARRERA', '=', 'Carrera.COD_CARRERA')
        ->distinct()
        ->get();

    $response = [];

    foreach ($mesasAsignadas as $mesa) {
        $codEleccion = $mesa->COD_ELECCION;
        $motivo = $mesa->MOTIVO_ELECCION;
        $fecha = $mesa->fecha_eleccion;
        $facultad = $mesa->nombre_facultad;
        $nombreCarrera = $mesa->nombre_carrera;

        if (!isset($response[$codEleccion])) {
            $response[$codEleccion] = [
                'motivo' => $motivo,
                'fecha_eleccion' => $fecha,
                'facultad' => $facultad,
                'carreras' => []
            ];
        }

        if (!isset($response[$codEleccion]['carreras'][$nombreCarrera])) {
            $totalMesas = Mesas::where('COD_ELECCION', $codEleccion)
                ->where('COD_CARRERA', $mesa->COD_CARRERA)
                ->count();

            $response[$codEleccion]['carreras'][$nombreCarrera] = [
                'nombre_carrera' => $nombreCarrera,
                'total_mesas_por_carrera' => $totalMesas
            ];
        }
    }

    // Reorganizar la respuesta para obtener el formato correcto
    $formattedResponse = [];
    foreach ($response as $codEleccion => $eleccionData) {
        $formattedResponse[] = [
            'motivo' => $eleccionData['motivo'],
            'facultad' => $eleccionData['facultad'],
            'fecha_eleccion' => $eleccionData['fecha_eleccion'],
            'carreras' => array_values($eleccionData['carreras'])
        ];
    }

    return response()->json($formattedResponse);
}

public function listarMesasAsignadasPorEleccion($idEleccion)
{
    $mesasAsignadas = Mesas::select(
        'mesas.COD_ELECCION',
        'elecciones.MOTIVO_ELECCION',
        'facultad.COD_FACULTAD',
        'facultad.nombre_facultad',
        'elecciones.fecha_eleccion',
        'carrera.COD_CARRERA',
        'carrera.nombre_carrera',
        'mesas.COD_MESA',
        'mesas.NUM_MESA',
        'mesas.APELLIDOS_ESTUDIANTES'
    )
    ->join('elecciones', 'mesas.COD_ELECCION', '=', 'elecciones.COD_ELECCION')
    ->join('facultad', 'mesas.COD_FACULTAD', '=', 'facultad.COD_FACULTAD')
    ->join('carrera', 'mesas.COD_CARRERA', '=', 'carrera.COD_CARRERA')
    ->where('mesas.COD_ELECCION', $idEleccion) // Usar el parámetro en lugar de un valor fijo
    ->distinct()
    ->get();

    $response = [];

    foreach ($mesasAsignadas as $mesa) {
        $codEleccion = $mesa->COD_ELECCION;
        $motivo = $mesa->MOTIVO_ELECCION;
        $fecha = $mesa->fecha_eleccion;
        $codFacultad = $mesa->COD_FACULTAD;
        $nombreFacultad = $mesa->nombre_facultad;
        $nombreCarrera = $mesa->nombre_carrera;
        $codMesa = $mesa->COD_MESA;
        $numeroMesa = $mesa->NUM_MESA;
        $apellidosEstudiantes = $mesa->APELLIDOS_ESTUDIANTES;

        if (!isset($response[$codEleccion])) {
            $response[$codEleccion] = [
                'motivo' => $motivo,
                'fecha_eleccion' => $fecha,
                'facultades' => []
            ];
        }

        if (!isset($response[$codEleccion]['facultades'][$codFacultad])) {
            $response[$codEleccion]['facultades'][$codFacultad] = [
                'nombre_facultad' => $nombreFacultad,
                'carreras' => []
            ];
        }

        if (!isset($response[$codEleccion]['facultades'][$codFacultad]['carreras'][$nombreCarrera])) {
            $totalMesas = Mesas::where('COD_ELECCION', $codEleccion)
                ->where('COD_CARRERA', $mesa->COD_CARRERA)
                ->count();

            $response[$codEleccion]['facultades'][$codFacultad]['carreras'][$nombreCarrera] = [
                'nombre_carrera' => $nombreCarrera,
                'total_mesas_por_carrera' => $totalMesas,
                'mesas' => []
            ];
        }

        $response[$codEleccion]['facultades'][$codFacultad]['carreras'][$nombreCarrera]['mesas'][] = [
            'cod_mesa' => $codMesa,
            'numero_mesa' => $numeroMesa,
            'apellidos_estudiantes' => $apellidosEstudiantes
        ];
    }

    // Reorganizar la respuesta para obtener el formato correcto
    $formattedResponse = [];
    foreach ($response as $codEleccion => $eleccionData) {
        $formattedResponse[] = [
            'motivo' => $eleccionData['motivo'],
            'fecha_eleccion' => $eleccionData['fecha_eleccion'],
            'facultades' => array_values($eleccionData['facultades'])
        ];
    }

    return response()->json([
        'mesasasignadas' => $formattedResponse,
        'asignacionExistente' => count($formattedResponse) > 0

    ]);
}


public function listarMesasAsignadasPorEleccionoriginal($idEleccion)
{
    $mesasAsignadas = Mesas::select(
        'Mesas.COD_ELECCION',
        'Eleccioness.MOTIVO_ELECCION',
        'Facultad.nombre_facultad',
        'Eleccioness.fecha_eleccion',
        'Carrera.COD_CARRERA',
        'Carrera.nombre_carrera',
        'Mesas.COD_MESA',
        'Mesas.NUM_MESA'
    )
        ->join('Eleccioness', 'Mesas.COD_ELECCION', '=', 'Eleccioness.COD_ELECCION')
        ->join('Facultad', 'Mesas.COD_FACULTAD', '=', 'Facultad.COD_FACULTAD')
        ->join('Carrera', 'Mesas.COD_CARRERA', '=', 'Carrera.COD_CARRERA')
        ->where('Mesas.COD_ELECCION', $idEleccion)
        ->distinct()
        ->get();

    $response = [];

    foreach ($mesasAsignadas as $mesa) {
        $codEleccion = $mesa->COD_ELECCION;
        $motivo = $mesa->MOTIVO_ELECCION;
        $fecha = $mesa->fecha_eleccion;
        $facultad = $mesa->nombre_facultad;
        $nombreCarrera = $mesa->nombre_carrera;
        $codMesa = $mesa->COD_MESA;
        $numeroMesa = $mesa->NUM_MESA;

        if (!isset($response[$codEleccion])) {
            $response[$codEleccion] = [
                'motivo' => $motivo,
                'fecha_eleccion' => $fecha,
                'facultad' => $facultad,
                'carreras' => []
            ];
        }

        if (!isset($response[$codEleccion]['carreras'][$nombreCarrera])) {
            $totalMesas = Mesas::where('COD_ELECCION', $codEleccion)
                ->where('COD_CARRERA', $mesa->COD_CARRERA)
                ->count();

            $response[$codEleccion]['carreras'][$nombreCarrera] = [
                'nombre_carrera' => $nombreCarrera,
                'total_mesas_por_carrera' => $totalMesas,
                'mesas' => []
            ];
        }

        $response[$codEleccion]['carreras'][$nombreCarrera]['mesas'][] = [
            'cod_mesa' => $codMesa,
            'numero_mesa' => $numeroMesa
        ];
    }

    // Reorganizar la respuesta para obtener el formato correcto
    $formattedResponse = [];
    foreach ($response as $codEleccion => $eleccionData) {
        $formattedResponse[] = [
            'motivo' => $eleccionData['motivo'],
            'facultad' => $eleccionData['facultad'],
            'fecha_eleccion' => $eleccionData['fecha_eleccion'],
            'carreras' => array_values($eleccionData['carreras'])
        ];
    }

    return response()->json($formattedResponse);
}


public function listarMesasAsignadasPorEleccion2($idEleccion)
{
    $mesasAsignadas = Mesas::select(
        'Mesas.COD_ELECCION',
        'Eleccioness.MOTIVO_ELECCION',
        'Facultad.nombre_facultad',
        'Eleccioness.fecha_eleccion',
        'Carrera.COD_CARRERA',
        'Carrera.nombre_carrera',
        'Mesas.COD_MESA',
        'Mesas.NUM_MESA'
    )
        ->join('Eleccioness', 'Mesas.COD_ELECCION', '=', 'Eleccioness.COD_ELECCION')
        ->join('Facultad', 'Mesas.COD_FACULTAD', '=', 'Facultad.COD_FACULTAD')
        ->join('Carrera', 'Mesas.COD_CARRERA', '=', 'Carrera.COD_CARRERA')
        ->where('Mesas.COD_ELECCION', $idEleccion)
        ->distinct()
        ->get();

    $response = [];

    foreach ($mesasAsignadas as $mesa) {
        $codEleccion = $mesa->COD_ELECCION;
        $motivo = $mesa->MOTIVO_ELECCION;
        $fecha = $mesa->fecha_eleccion;
        $facultad = $mesa->nombre_facultad;
        $nombreCarrera = $mesa->nombre_carrera;
        $codMesa = $mesa->COD_MESA;
        $numeroMesa = $mesa->NUM_MESA;

        if (!isset($response[$codEleccion])) {
            $response[$codEleccion] = [
                'motivo' => $motivo,
                'fecha_eleccion' => $fecha,
                'facultad' => $facultad,
                'carreras' => []
            ];
        }

        if (!isset($response[$codEleccion]['carreras'][$nombreCarrera])) {
            $response[$codEleccion]['carreras'][$nombreCarrera] = [
                'nombre_carrera' => $nombreCarrera,
                'mesas' => []
            ];
        }

        $response[$codEleccion]['carreras'][$nombreCarrera]['mesas'][] = [
            'cod_mesa' => $codMesa,
            'numero_mesa' => $numeroMesa
        ];
    }

    // Reorganizar la respuesta para obtener el formato correcto
    $formattedResponse = [];
    foreach ($response as $codEleccion => $eleccionData) {
        $formattedResponse[] = [
            'motivo' => $eleccionData['motivo'],
            'facultad' => $eleccionData['facultad'],
            'fecha_eleccion' => $eleccionData['fecha_eleccion'],
            'carreras' => array_values($eleccionData['carreras'])
        ];
    }

    return response()->json($formattedResponse);
}




public function agregarNuevaMesa(Request $request)
{
    $cod_eleccion = $request->input('COD_ELECCION');
    $cod_facultad = $request->input('COD_FACULTAD');
    $cod_carrera = $request->input('COD_CARRERA');

    // Obtener el último número de mesa para una carrera en una elección
    $ultimaMesa = Mesas::where('COD_ELECCION', $cod_eleccion)
        ->where('COD_FACULTAD', $cod_facultad)
        ->where('COD_CARRERA', $cod_carrera)
        ->orderBy('NUM_MESA', 'desc')
        ->first();

    // Aumentar el número de mesa
    $nuevoNumeroMesa = $ultimaMesa ? $ultimaMesa->NUM_MESA + 1 : 1;

    $cant_est_mesa = $request->input('CANT_EST_MESA');

    // Crear y guardar la nueva mesa con el nuevo número
    $nuevaMesa = new Mesas();
    $nuevaMesa->COD_ELECCION = $cod_eleccion;
    $nuevaMesa->COD_FACULTAD = $cod_facultad;
    $nuevaMesa->COD_CARRERA = $cod_carrera;
    $nuevaMesa->NUM_MESA = $nuevoNumeroMesa;
    $nuevaMesa->CANT_EST_MESA = $cant_est_mesa;
    $nuevaMesa->save();

    return response()->json(['message' => 'Nueva mesa agregada con éxito', 'num_mesa' => $nuevoNumeroMesa]);
}




public function generarPDFMesasAsignadasPorEleccion($idEleccion)
{
    $mesasAsignadas = Mesas::select(
        'mesas.COD_ELECCION',
        'elecciones.MOTIVO_ELECCION',
        'facultad.COD_FACULTAD',
        'facultad.nombre_facultad',
        'elecciones.fecha_eleccion',
        'carrera.COD_CARRERA',
        'carrera.nombre_carrera',
        'mesas.COD_MESA',
        'mesas.NUM_MESA',
        'mesas.APELLIDOS_ESTUDIANTES'
    )
    ->join('elecciones', 'mesas.COD_ELECCION', '=', 'elecciones.COD_ELECCION')
    ->join('facultad', 'mesas.COD_FACULTAD', '=', 'facultad.COD_FACULTAD')
    ->join('carrera', 'mesas.COD_CARRERA', '=', 'carrera.COD_CARRERA')
    ->where('mesas.COD_ELECCION', $idEleccion)
    ->distinct()
    ->get();

    // Generar el HTML para el PDF
    $html = '<style>
        body { font-family: Arial, sans-serif; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>';

    $html .= '<h1 style="text-align: center; margin-top: 20px;">Mesas Asignadas por Elección</h1>';
    $html .= '<table>';
    $html .= '<tr>';
    $html .= '<th>Motivo de Elección</th>';
    $html .= '<th>Fecha de Elección</th>';
    $html .= '<th>Facultad</th>';
    $html .= '<th>Carrera</th>';
    $html .= '<th>Número de Mesa</th>';
    $html .= '<th>Apellidos de Estudiantes</th>';
    $html .= '</tr>';

    foreach ($mesasAsignadas as $mesa) {
        $html .= '<tr>';
        $html .= '<td>' . $mesa->MOTIVO_ELECCION . '</td>';
        $html .= '<td>' . $mesa->fecha_eleccion . '</td>';
        $html .= '<td>' . $mesa->nombre_facultad . '</td>';
        $html .= '<td>' . $mesa->nombre_carrera . '</td>';
        $html .= '<td>' . $mesa->NUM_MESA . '</td>';
        $html .= '<td>' . $mesa->APELLIDOS_ESTUDIANTES . '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';

    // Generar el PDF
    $dompdf = new Dompdf();
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $dompdf->setOptions($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $output = $dompdf->output();

    return response()->json(['pdf' => base64_encode($output)]);
}



        /*$mesasAsignadas = Mesas::select('Mesas.*', 'eleccioness.MOTIVO_ELECCION', 'facultad.nombre_facultad', 'carrera.nombre_carrera')
            ->join('eleccioness', 'mesas.COD_ELECCION', '=', 'eleccioness.COD_ELECCION')
            ->join('facultad', 'mesas.COD_FACULTAD', '=', 'facultad.COD_FACULTAD')
            ->join('carrera', 'mesas.COD_CARRERA', '=', 'carrera.COD_CARRERA')
            ->get();

        return response()->json($mesasAsignadas);*/


    // Otros métodos del controlador según sea necesario
}
