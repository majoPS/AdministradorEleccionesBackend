<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListaVotante;
use App\Models\Mesas;
use App\Models\Poblacion;
use App\Models\Facultad;
use App\Models\Carrera;
use App\Models\PoblacionFacuCarr;
use Illuminate\Support\Facades\DB;

use Dompdf\Dompdf;
use Dompdf\Options;


class ListaVotantesController extends Controller
{
    public function index()
    {
        $listasVotantes = ListaVotante::all();
        return response()->json(['message' => $listasVotantes]);
    }

    // Otros métodos del controlador (store, show, edit, update, destroy) según tus necesidades
    public function store(Request $request)
    {
        // Validar los datos del formulario (puedes personalizar las reglas según tus necesidades)
        $request->validate([
            'cod_mesa' => 'required|integer',
            'carnetidentidad' => 'required|string|max:255',
            'firma' => 'required|string',
        ]);

        // Crear una nueva instancia de ListaVotante con los datos del formulario
        $listaVotante = ListaVotante::create([
            'cod_mesa' => $request->input('cod_mesa'),
            'carnetidentidad' => $request->input('carnetidentidad'),
            'firma' => $request->input('firma'),
        ]);

        // Puedes devolver la nueva instancia creada como respuesta
        return response()->json(['message' => 'Lista de votante creada exitosamente', 'data' => $listaVotante], 201);
    }


// En ListaVotanteController.php-----------ESTE ES OFICIAL

public function generarListasVotantes($codEleccion)
{
    // 1. Obtener la información de la elección
    $mesas = Mesas::where('cod_eleccion', $codEleccion)->get();

    // 2. Recorrer las mesas y realizar las operaciones necesarias
    foreach ($mesas as $mesa) {
        // Obtener información adicional de la mesa
        $facultad = Facultad::find($mesa->COD_FACULTAD);
        $carrera = Carrera::find($mesa->COD_CARRERA);

        // Obtener población de estudiantes y docentes para la facultad y carrera
        $poblacionFacuCarr = PoblacionFacuCarr::where([
            'cod_facultad' => $mesa->COD_FACULTAD,
            'cod_carrera' => $mesa->COD_CARRERA
        ])->get();

        // Filtrar población por estudiantes y docentes
        $estudiantes = Poblacion::whereIn('CODSIS', $poblacionFacuCarr->pluck('codsis'))
            ->where('estudiante', true)
            ->get();

        $docentes = Poblacion::whereIn('CODSIS', $poblacionFacuCarr->pluck('codsis'))
            ->where('docente', true)
            ->get();

        // Obtener el rango de apellidos desde la cadena de la mesa
        $rangoApellidos = $this->obtenerRangoApellidos($mesa);
        //$rangoInicial = strtoupper(substr(trim($rangoApellidos[0]), 0, 1));
        //$rangoFinal = strtoupper(substr(trim($rangoApellidos[1]), 0, 1));
        //return response()->json($rangoApellidos);

        // Comparar apellidos y asignar a ListasVotantes
        $this->asignarListasVotantesPorRango($mesa, $estudiantes, $rangoApellidos);
        $this->asignarListasVotantesPorRango($mesa, $docentes, $rangoApellidos);
    }

    return response()->json(['message' => 'Listas de votantes generadas exitosamente.']);
}

public function asignarListasVotantesPorRango($mesa, $poblacion, $rangoApellidos)
{
    if (count($rangoApellidos) !== 2) {
        return response()->json(['error' => 'El formato del rango de apellidos no es válido.']);
    }

    $rangoInicial = strtoupper(substr(trim($rangoApellidos[0]), -1)); // Obtener la última letra
    $rangoFinal = strtoupper(substr(trim($rangoApellidos[1]), -1));   // Obtener la última letra



    // Recorrer la población y asignar a ListaVotante si cumple con el rango
    foreach ($poblacion as $persona) {
        // Obtener la primera letra del apellido
        $primeraLetra = strtoupper(substr($persona->APELLIDO, 0, 1));

        // Verificar si la primera letra está en el rango definido por la mesa
        if (strcmp($primeraLetra, $rangoInicial) >= 0 && strcmp($primeraLetra, $rangoFinal) <= 0) {
            // Asignar a ListaVotante
            ListaVotante::create([
                'cod_mesa' => $mesa->COD_MESA,
                'carnetidentidad' => $persona->CARNETIDENTIDAD,
                'firma' => 'FirmaEjemplo', // Puedes ajustar este valor según tus necesidades
            ]);
        }
    }

    // Retornar una respuesta exitosa
    return response()->json(['message' => 'Listas de votantes asignadas exitosamente.']);
}





    private function obtenerRangoApellidos($mesa)
    {
        // Modificar según la estructura de tu modelo Mesa y el atributo que contiene el rango
        // Por ejemplo, si el atributo se llama 'apellidos', reemplaza 'apellido_estudiantes' con 'apellidos'
        return explode('-', $mesa->APELLIDOS_ESTUDIANTES);
    }


    public function obtenerDatosPorMesa2($codigoMesa)
    {
        $resultados = DB::table('mesas')
            ->where('mesas.cod_mesa', $codigoMesa)
            ->join('poblacion_facu_carr', 'mesas.cod_facultad', '=', 'poblacion_facu_carr.cod_facultad')
            ->join('poblacion', 'poblacion_facu_carr.codsis', '=', 'poblacion.codsis')
            ->join('carrera', 'poblacion_facu_carr.cod_carrera', '=', 'carrera.cod_carrera')
            ->select('poblacion.carnetidentidad', 'poblacion.nombre', 'poblacion.apellido', 'carrera.nombre_carrera')
            ->get();

        return response()->json(['resultados' => $resultados]);
    }

    //ESTE ESTE OFICIAL
    public function obtenerDatosPorMesaYGenerarPDF($codigoMesa)
    {
        $datos = DB::table('listas_votantes')
        ->join('mesas', 'listas_votantes.cod_mesa', '=', 'mesas.COD_MESA')
        ->join('poblacion', 'listas_votantes.carnetidentidad', '=', 'poblacion.CARNETIDENTIDAD')
        ->select('listas_votantes.carnetidentidad', 'poblacion.NOMBRE as nombre', 'poblacion.APELLIDO as apellido')
        ->where('mesas.COD_MESA', '=', $codigoMesa)
        ->orderBy('poblacion.APELLIDO') // Ordena por apellido en orden ascendente (por defecto)
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
            td.firma { /* Estilos específicos para la celda de firma */
                width: 100px; /* Ajusta el ancho según tus necesidades */
            }
        </style>';

        $html .= '<h1 style="text-align: center; margin-top: 20px;">Lista de Votantes de la Mesa</h1>';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th>Carnet de Identidad</th>';
        $html .= '<th>Apellido</th>';
        $html .= '<th>Nombre</th>';
        $html .= '<th class="firma">Firma</th>'; // Aplica la clase "firma" para estilos específicos
        $html .= '</tr>';

        foreach ($datos as $dato) {
            $html .= '<tr>';
            $html .= '<td>' . $dato->carnetidentidad . '</td>';
            $html .= '<td>' . $dato->apellido . '</td>';
            $html .= '<td>' . $dato->nombre . '</td>';
            $html .= '<td class="firma"></td>'; // Aplica la clase "firma" para estilos específicos
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



    public function obtenerDatosPorMesa($codigoMesa)
    {
        $datos = DB::table('listas_votantes')
            ->join('mesas', 'listas_votantes.cod_mesa', '=', 'mesas.cod_mesa')
            ->join('poblacion', 'listas_votantes.carnetidentidad', '=', 'poblacion.carnetidentidad')
            ->join('poblacion_facu_carr', 'poblacion.codsis', '=', 'poblacion_facu_carr.codsis')
            ->join('carrera', 'poblacion_facu_carr.cod_carrera', '=', 'carrera.cod_carrera')
            ->join('facultad', 'poblacion_facu_carr.cod_facultad', '=', 'facultad.cod_facultad')
            ->select(
                'listas_votantes.carnetidentidad',
                'poblacion.nombre',
                'poblacion.apellido',
                'carrera.nombre_carrera as carrera',
                'facultad.nombre_facultad as facultad'
            )
            ->where('mesas.cod_mesa', '=', $codigoMesa)
            ->get();

        return response()->json(['datos' => $datos]);
    }


}
