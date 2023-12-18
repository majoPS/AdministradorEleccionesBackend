<?php

namespace App\Http\Controllers;

use App\Models\ConvocatoriaElecciones;
use App\Models\Elecciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Dompdf\Dompdf;
use Dompdf\Options;

class ConvocatoriaEleccionesController extends Controller
{

    //validaciones 
    public function verificarConvocatoria($idEleccion)
    {
        try {
            // Buscar la convocatoria basada en el ID de la elección
            $convocatoria = ConvocatoriaElecciones::where('id_eleccion', $idEleccion)->first();

            // Si se encuentra la convocatoria, devolver true; de lo contrario, devolver false
            return response()->json(['existeConvocatoria' => $convocatoria ? true : false]);

        } catch (\Exception $e) {
            // Manejar errores según tus necesidades
            return response()->json(['error' => 'Error al verificar la convocatoria'], 500);
        }
    }



    //funciones
    public function index()
    {
        $convocatorias = ConvocatoriaElecciones::all();
        return response()->json($convocatorias);
    }

    public function obtenerIdConvocatoria($idEleccion)
    {
        $convocatoria = ConvocatoriaElecciones::where('id_eleccion', $idEleccion)->first();

        if ($convocatoria) {
            return response()->json(['id_convocatoria' => $convocatoria->id_convocatoria]);
        } else {
            return response()->json(['message' => 'No se encontró la convocatoria para la elección proporcionada']);
        }
    }

    public function store(Request $request)
    {
        $convocatoria = new ConvocatoriaElecciones();
        $convocatoria->fecha_inicio = $request->input('fecha_inicio');
        $convocatoria->fecha_fin = $request->input('fecha_fin');
        $convocatoria->motivo = $request->input('motivo');
        $convocatoria->descripcion = $request->input('descripcion');
        $convocatoria->requisitos = $request->input('requisitos');
        $convocatoria->id_eleccion = $request->input('id_eleccion');
        $convocatoria->tipo = $request->input('tipo');
        $convocatoria->candidatos = $request->input('candidatos');
        $convocatoria->estado = $request->input('estado');
        $convocatoria->restricciones = $request->input('restricciones');
        $convocatoria->contacto = $request->input('contacto');
        $convocatoria->lugar = $request->input('lugar');

        $convocatoria->save();

        return response()->json($convocatoria);
    }

    public function update(Request $request, $id)
    {
       // $convocatoria = ConvocatoriaElecciones::find($id);
       $convocatoria = ConvocatoriaElecciones::where('id_eleccion', $id)->first();

        if (!$convocatoria) {
            return response()->json(['message' => 'Convocatoria no encontrada'], 404);
        }

        $convocatoria->fill($request->all());
        $convocatoria->save();

        return response()->json($convocatoria);
    }

    public function show($id)
    {
       // $convocatoria = ConvocatoriaElecciones::find($id);
       $convocatoria = ConvocatoriaElecciones::where('id_eleccion', $id)->first();
        return response()->json($convocatoria);
    }
    public function show2($id)
    {
        $convocatoria = ConvocatoriaElecciones::find($id);
       //$convocatoria = ConvocatoriaElecciones::where('id_eleccion', $id)->first();
        return response()->json($convocatoria);
    }

  
    public function destroy($id)
    {
        $convocatoria = ConvocatoriaElecciones::find($id);
        $convocatoria->delete();
        return response()->json(['message' => 'Convocatoria eliminada']);
    }






   
    
    public function generarPDF2($id)
    {
        $convocatoria = ConvocatoriaElecciones::find($id);
    
        $dompdf = new Dompdf();
        $dompdf->loadHtml('<h1>Contenido del PDF aquí</h1>');
    
        // (Opcional) Personalizar el PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
    
        $dompdf->render();
        $output = $dompdf->output();
    
        return response()->json(['pdf' => base64_encode($output)]);
    }
    

    public function generarPDF($id)
    {


      


        // Obtener los datos de la convocatoria
        //$convocatoria = ConvocatoriaElecciones::find($id);
        $convocatoria = ConvocatoriaElecciones::where('id_eleccion', $id)->first();

        $eleccion = Elecciones::where('COD_ELECCION', $convocatoria->id_eleccion)->first();


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
        img {
            max-width: 200px;
            margin: auto;
            display: block;
        }
    </style>';
    
   

        // HTML con los datos de la convocatoria
        $html = '<style>body { font-family: Arial, sans-serif; }</style>';
        $html .= '<img src="' . public_path('assets/logo.png') . '" alt="Logo Universidad" style="width: 200px;">';
       
        $html .= '<h1 style="text-align: center; margin-top: 20px;">Universidad Mayor de San Simón</h1>';
        $html .= '<h2 style="text-align: center; margin-top: 30px;">Información de la Convocatoria</h2>';

        //$html .= '<p><strong>Motivo:</strong> ' . $convocatoria->motivo . '</p>';
        $html .= '<h3>Fecha Inicio convocatoria:</h3>';
        $html .= '<p>' . $eleccion->FECHA_INI_CONVOCATORIA . '</p>';
        $html .= '<h3>Fecha Fin convocatoria:</h3>';
        $html .= '<p>' . $eleccion->FECHA_FIN_CONVOCATORIA . '</p>';
        
        $html .= '<h3>Motivo:</h3>';
        $html .= '<p>' . $convocatoria->motivo . '</p>';
        
        $html .= '<h3>Descripción:</h3>';
        $html .= '<p>' . $convocatoria->descripcion . '</p>';
        
        $html .= '<h3>Requisitos:</h3>';
        $html .= '<p>' . $convocatoria->requisitos . '</p>';
      
    

        $html .= '<table style="width: 100%; border-collapse: collapse; border: 1px solid black;">';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Fecha Asignacion de Comite:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->fecha_inicio . '</td>';
        $html .= '</tr>';
        
        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Fecha de asignacion mesas:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->fecha_fin . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Tipo Eleccion:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->tipo . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Cantidad de :</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->candidatos . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Estado de la eleccion:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->candidatos . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Restricciones de la eleccion:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->restricciones . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Contaco eleccion:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->contacto . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Lugar eleccion:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->lugar . '</td>';
        $html .= '</tr>';
        


        // Agregar más filas con el mismo formato para cada campo y valor
        
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

    public function generarPDF_Publicado($id)
    {


      


        // Obtener los datos de la convocatoria
        $convocatoria = ConvocatoriaElecciones::find($id);
        //$convocatoria = ConvocatoriaElecciones::where('id_eleccion', $id)->first();

        $eleccion = Elecciones::where('COD_ELECCION', $convocatoria->id_eleccion)->first();


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
        img {
            max-width: 200px;
            margin: auto;
            display: block;
        }
    </style>';
    
   

        // HTML con los datos de la convocatoria
        $html = '<style>body { font-family: Arial, sans-serif; }</style>';
        $html .= '<img src="' . public_path('assets/logo.png') . '" alt="Logo Universidad" style="width: 200px;">';
       
        $html .= '<h1 style="text-align: center; margin-top: 20px;">Universidad Mayor de San Simón</h1>';
        $html .= '<h2 style="text-align: center; margin-top: 30px;">Información de la Convocatoria</h2>';

        //$html .= '<p><strong>Motivo:</strong> ' . $convocatoria->motivo . '</p>';
        $html .= '<h3>Fecha Inicio convocatoria:</h3>';
        $html .= '<p>' . $eleccion->FECHA_INI_CONVOCATORIA . '</p>';
        $html .= '<h3>Fecha Fin convocatoria:</h3>';
        $html .= '<p>' . $eleccion->FECHA_FIN_CONVOCATORIA . '</p>';
        
        $html .= '<h3>Motivo:</h3>';
        $html .= '<p>' . $convocatoria->motivo . '</p>';
        
        $html .= '<h3>Descripción:</h3>';
        $html .= '<p>' . $convocatoria->descripcion . '</p>';
        
        $html .= '<h3>Requisitos:</h3>';
        $html .= '<p>' . $convocatoria->requisitos . '</p>';
      
    

        $html .= '<table style="width: 100%; border-collapse: collapse; border: 1px solid black;">';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Fecha Asignacion de Comite:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->fecha_inicio . '</td>';
        $html .= '</tr>';
        
        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Fecha de asignacion mesas:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->fecha_fin . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Tipo Eleccion:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->tipo . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Cantidad de :</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->candidatos . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Estado de la eleccion:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->candidatos . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Restricciones de la eleccion:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->restricciones . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Contaco eleccion:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->contacto . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th style="text-align: left; border: 1px solid black; padding: 8px;">Lugar eleccion:</th>';
        $html .= '<td style="border: 1px solid black; padding: 8px;">' . $convocatoria->lugar . '</td>';
        $html .= '</tr>';
        


        // Agregar más filas con el mismo formato para cada campo y valor
        
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

    /*public function generarPDF($id)
    {
        // Obtener los datos de la convocatoria
        $convocatoria = ConvocatoriaElecciones::find($id);

        // Crear el HTML para el PDF con los datos de la convocatoria
        $html = '<h1>Información de la Convocatoria</h1>';
        $html .= '<p>Fecha de Inicio: ' . $convocatoria->fecha_inicio . '</p>';
        $html .= '<p>Fecha de Fin: ' . $convocatoria->fecha_fin . '</p>';
        // Agrega más campos según sea necesario

        // Generar el PDF
        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Descargar el PDF
        $dompdf->stream('convocatoria_' . $id . '.pdf', ['Attachment' => 0]);
    }
*/

}
