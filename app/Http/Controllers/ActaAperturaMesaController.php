<?php

// app\Http\Controllers\ActaAperturaMesaController.php

namespace App\Http\Controllers;

//use App\Models\ConvocatoriaElecciones;
use App\Models\Elecciones;
use Illuminate\Http\Request;
use App\Models\ActaAperturaMesa;
use App\Models\Frente;
use App\Models\Mesas;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;

class ActaAperturaMesaController extends Controller
{
    // Método para mostrar todas las actas de apertura de mesas
    public function index()
    {
        $actas = ActaAperturaMesa::all();
        return response()->json(['actas' => $actas]);
    }

    // Método para almacenar una nueva acta de apertura de mesa
   /*  public function store(Request $request)
    {
        $acta = ActaAperturaMesa::create($request->all());
        return response()->json(['acta' => $acta], 201);
    }
 */

 public function generarPDFActaCierreMesas($cod_mesa)
 {
     $actaCierre = ActaAperturaMesa::where('cod_mesa', $cod_mesa)->first();

     // Obtener la elección asociada a la mesa
     $eleccion = Mesas::where('COD_MESA', $cod_mesa)->value('COD_ELECCION');

     // Obtener los frentes asociados a esa elección
     $frentes = DB::table('elecciones_frente')
         ->join('frente', 'elecciones_frente.COD_FRENTE', '=', 'frente.COD_FRENTE')
         ->where('elecciones_frente.COD_ELECCION', $eleccion)
         ->select('frente.COD_FRENTE', 'frente.NOMBRE_FRENTE', 'frente.SIGLA_FRENTE')
         ->get();

     // Estilo CSS
     $html = '<style>
         body { font-family: Arial, sans-serif; }
         table { width: 100%; border-collapse: collapse; margin-top: 20px; }
         th, td { border: 1px solid #000; padding: 10px; text-align: left; }
         th { background-color: #f2f2f2; }
         h1, h2, h3 { text-align: center; margin-top: 20px; }
         .firmas { margin-top: 30px; }
         .firmas td { padding: 20px; text-align: center; }
         .cuerpo { margin-top: 30px; }
         .cuerpo p { margin-bottom: 15px; }
         .representantes { margin-top: 30px; }
         .representantes th, .representantes td { padding: 15px; }
         .votos { width: 60px; }
     </style>';

     // HTML con los datos del acta
     $html .= '<img src="' . public_path('assets/logo.png') . '" alt="Logo Universidad" style="width: 200px;">';
     $html .= '<h1>Universidad Mayor de San Simón</h1>';
     $html .= '<h2>Acta de Cierre de Mesas</h2>';

     // Sección de Datos del Acta de Cierre
     $html .= '<h3>Datos del Acta de Cierre</h3>';
     $html .= '<table>';
     $html .= '<tr><th>Código de Mesa</th><td>' . $actaCierre->cod_mesa . '</td></tr>';
     $html .= '<tr><th>Hora de Cierre de Votación</th><td>' . $actaCierre->hora_fin_votacion . '</td></tr>';
     $html .= '<tr><th>Día de Cierre de Votación</th><td>' . $actaCierre->dia_instalacion_mesa . '</td></tr>';
     $html .= '</table>';

     // Sección de Resultados por Frente
     $html .= '<h3>Resultados por Frente</h3>';
     $html .= '<p>A horas........., transcurridas.........horas de votación continua se procedió al cierre de la MESA Nº'. $actaCierre->cod_mesa .'
     efectuándose inmediatamente el escrutinio de votos, con los siguientes resultados</p>';

     $html .= '<table>';
     $html .= '<tr><th>Frente</th><th>Votos</th></tr>';

     foreach ($frentes as $frente) {
         $html .= '<tr>';
         $html .= '<td>' . $frente->NOMBRE_FRENTE . '</td>';
         $html .= '<td><input class="votos" type="text" name="votos_' . $frente->COD_FRENTE . '"></td>';
         $html .= '</tr>';
     }

     $html .= '</table>';

     // Sección de Votos Válidos, Blancos, Nulos y Total de Votos Emitidos
     $html .= '<h3>Votos Válidos, Blancos, Nulos y Total de Votos Emitidos</h3>';
     $html .= '<table>';
     $html .= '<tr>';
     $html .= '<th>Votos Válidos</th>';
     $html .= '<th>Votos en Blanco</th>';
     $html .= '<th>Votos Nulos</th>';
     $html .= '<th>Total de Votos Emitidos</th>';
     $html .= '</tr>';
     $html .= '<tr>';
     $html .= '<td><input class="votos" type="text" name="votos_validos"></td>';
     $html .= '<td><input class="votos" type="text" name="votos_blancos"></td>';
     $html .= '<td><input class="votos" type="text" name="votos_nulos"></td>';
     $html .= '<td><input class="votos" type="text" name="votos_totales"></td>';
     $html .= '</tr>';
     $html .= '</table>';

     $html .= '<h3>Representantes de la Mesa</h3>';
     $html .= '<table class="representantes">';
     $html .= '<tr><th>Presidente de Mesa</th><td style="width: 50%;"> </td></tr>';
     $html .= '<tr><th>Delegado Docente</th><td>  </td></tr>';
     $html .= '<tr><th>Delegado Docente</th><td>  </td></tr>';
     $html .= '<tr><th>Delegado Estudiante</th><td></td></tr>';
     $html .= '<tr><th>Delegado Estudiante</th><td></td></tr>';
     $html .= '</table>';

     // Puedes agregar más secciones y detalles según sea necesario

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

    public function store(Request $request)
    {
        $acta = new ActaAperturaMesa();
        $acta->cod_mesa  = $request->input('cod_mesa');
        $acta->hora_instalacion_mesa = $request->input('hora_instalacion_mesa');
        $acta->hora_inicio_votacion = $request->input('hora_fin_votacion');
        $acta->hora_fin_votacion = $request->input('hora_fin_votacion');
        $acta->dia_instalacion_mesa = $request->input('dia_instalacion_mesa');
        $acta->miembros = $request->input('miembros');
        $acta->tipo_eleccion = $request->input('tipo_eleccion');


        $acta->save();

        return response()->json($acta);
    }


    public function generarPDFActaMesa($cod_mesa)
    {

        // Obtener los datos de la convocatoria
        //$convocatoria = ConvocatoriaElecciones::find($id);
        $acta = ActaAperturaMesa::where('cod_mesa', $cod_mesa)->first();

        //$eleccion = Elecciones::where('COD_ELECCION', $convocatoria->id_eleccion)->first();

// Estilo CSS
$html = '<style>
    body { font-family: Arial, sans-serif; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 10px; text-align: left; }
    th { background-color: #f2f2f2; }
    h1, h2, h3 { text-align: center; margin-top: 20px; }
    .firmas { margin-top: 30px; }
    .firmas td { padding: 20px; text-align: center; }
    .cuerpo { margin-top: 30px; }
    .cuerpo p { margin-bottom: 15px; }
    .representantes { margin-top: 30px; }
    .representantes th, .representantes td { padding: 15px; }
</style>';

// HTML con los datos del acta
$html .= '<img src="' . public_path('assets/logo.png') . '" alt="Logo Universidad" style="width: 200px;">';
$html .= '<h1>Universidad Mayor de San Simón</h1>';
$html .= '<h2>Acta de Apertura de Mesa</h2>';

// Sección de Datos del Acta
$html .= '<h3>Datos del Acta</h3>';
$html .= '<table>';
$html .= '<tr><th>Código de Mesa</th><td>' . $acta->cod_mesa . '</td></tr>';
$html .= '<tr><th>Hora de Instalación de Mesa</th><td>' . $acta->hora_instalacion_mesa . '</td></tr>';
$html .= '<tr><th>Hora de Inicio de Votación</th><td>' . $acta->hora_inicio_votacion . '</td></tr>';
$html .= '<tr><th>Día de Instalación de Mesa</th><td>' . $acta->dia_instalacion_mesa . '</td></tr>';
$html .= '</table>';

// Cuerpo del acta
$html .= '<div class="cuerpo">';
$html .= '<p>En la ciudad de Cochabamba, siendo las ' . $acta->hora_instalacion_mesa . ', de conformidad a lo establecido
por la Convocatoria y el Reglamento Electoral Universitario, se dio inicio al funcionamiento de la MESA: ' . $acta->cod_mesa . '.</p>';
// Puedes agregar más contenido según sea necesario
$html .= '</div>';

// Sección de Representantes de la Mesa
// Sección de Representantes de la Mesa
$html .= '<h3>Representantes de la Mesa</h3>';
$html .= '<table class="representantes">';
$html .= '<tr><th>Presidente de Mesa</th><td style="width: 50%;"></td></tr>';
$html .= '<tr><th>Delegado Docente</th><td></td></tr>';
$html .= '<tr><th>Delegado Docente</th><td></td></tr>';
$html .= '<tr><th>Delegado Estudiante</th><td></td></tr>';
$html .= '<tr><th>Delegado Estudiante</th><td></td></tr>';
$html .= '</table>';




        // Agrega más campos según sea necesario

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

        // Descargar el PDF
        //$dompdf->stream('convocatoria_' . $id . '.pdf', ['Attachment' => 0]);
    }




    // Otros métodos según tus necesidades (update, show, destroy, etc.)
}
