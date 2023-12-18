<?php

namespace App\Http\Controllers;

use App\Models\Elecciones;
use App\Models\EleccionesFrente;
use App\Models\Frente;
use App\Models\Candidato;
use App\Models\Boleta;
use App\Models\Poblacion;
use Illuminate\Http\Request;

use Dompdf\Dompdf;
use Dompdf\Options;

class GenerarBoletasController extends Controller
{
    public function generarBoletas($idEleccion)
    {
        $eleccion = Elecciones::find($idEleccion);

        if (!$eleccion) {
            abort(404, 'Elección no encontrada.');
        }

        $frentesIds = EleccionesFrente::where('COD_ELECCION', $idEleccion)->pluck('COD_FRENTE');
        $frentes = Frente::whereIn('COD_FRENTE', $frentesIds)->get();


        foreach ($frentes as $frente) {
            $candidatos = Candidato::where('COD_FRENTE', $frente->COD_FRENTE)->get();

            foreach ($candidatos as $candidato) {
                $poblacion = Poblacion::where('CARNETIDENTIDAD', $candidato->COD_CARNETIDENTIDAD)->first();
                //return response()->json($poblacion);
                if ($poblacion) {
                    $boleta = new Boleta([
                        'COD_ELECCION' => $eleccion->COD_ELECCION,
                        'COD_FRENTE' => $frente->COD_FRENTE,
                        'COD_CANDIDATO' => $candidato->COD_CANDIDATO,
                        'NOMBRE_UNIVERSIDAD' => 'Universidad Mayor de San Simón',
                        'NOMBRE_FRENTE' => $frente->NOMBRE_FRENTE,
                        'NOMBRE_CANDIDATO' => $poblacion->NOMBRE,
                        'APELLIDO_CANDIDATO' => $poblacion->APELLIDO,
                        'COD_CARNETIDENTIDAD' => $candidato->COD_CARNETIDENTIDAD,
                        'CARGO_POSTULADO' => $candidato->CARGO_POSTULADO,
                        'HABILITADO' => $candidato->HABILITADO,
                    ]);

                    $boleta->save();
                } else {
                    // Handle the case where no population is found
                }
            }
        }
    }
    public function verificarExistenciaBoletaPorEleccion($codEleccion)
    {
    // Utiliza el modelo 'Boleta' para realizar la consulta
    $existeBoleta = Boleta::where('COD_ELECCION', $codEleccion)->exists();

    return response()->json(['existeBoleta' => $existeBoleta]);
    }

    public function generarPDF($idEleccion)
    {
        $boletas = Boleta::where('COD_ELECCION', $idEleccion)->get();
        $frentes = Frente::where('COD_ELECCION', $idEleccion)->get();

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

        $html .= '<h1 style="text-align: center; margin-top: 20px;">Universidad Mayor de San Simón</h1>';
        $html .= '<h2 style="text-align: center; margin-top: 30px;">Boletas de Elección</h2>';

        foreach ($frentes as $frente) {
            $html .= '<h2 style="margin-top: 20px;">Frente: ' . $frente->NOMBRE_FRENTE . '</h2>';
            $boletasFrente = $boletas->where('COD_FRENTE', $frente->COD_FRENTE);

            $html .= '<table>';
            $html .= '<tr>';
            $html .= '<th>Casilla</th>';
            $html .= '<th>Nombre Candidato</th>';
            $html .= '<th>Cargo Postulado</th>';
            $html .= '<th>Carnet de Identidad</th>';
            $html .= '</tr>';

            foreach ($boletasFrente as $boleta) {
                $html .= '<tr>';
                $html .= '<td>' . $boleta->CASILLA . '</td>';
                $html .= '<td>' . $boleta->NOMBRE_CANDIDATO . '-' . $boleta->APELLIDO_CANDIDATO . '</td>';
                $html .= '<td>' . $boleta->CARGO_POSTULADO . '</td>';
                $html .= '<td>' . $boleta->COD_CARNETIDENTIDAD . '</td>';
                $html .= '</tr>';
            }

            $html .= '</table>';
        }

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


    public function generarBoletasPDF($idEleccion)
    {
        // Obtener las boletas para la elección dada
        $eleccion = Elecciones::find($idEleccion);

        if (!$eleccion) {
            return response()->json(['message' => 'No se encontró la elección'], 404);
        }
        $boletas = Boleta::where('COD_ELECCION', $idEleccion)->get();

        if ($boletas->isEmpty()) {
            return response()->json(['error' => 'No se encontraron boletas para la elección dada.']);
        }

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
            .checkbox {
                border: 1px solid #000;
                width: 15px;
                height: 15px;
                margin: auto; /* Añadido para centrar el contenido */
            }
            .votar-cell, .casilla-cell {
                border: 1px solid #000;
                text-align: center; /* Añadido para centrar el contenido */
                padding: 8px;
            }
        </style>';

        $html .= '<h1 style="text-align: center; margin-top: 20px;">Boletas de la Elección</h1>';

        // Obtener la lista de frentes únicos
        $frentesUnicos = $boletas->unique('COD_FRENTE')->pluck('COD_FRENTE');

        $numeroCasillaPrincipal = 1;

        foreach ($frentesUnicos as $frenteUnico) {
            // Filtrar boletas por frente
            $boletasFrente = $boletas->where('COD_FRENTE', $frenteUnico);

            $html .= '<h2>Frente: ' . $boletasFrente->first()->NOMBRE_FRENTE . '</h2>';
            $html .= '<table>';
            $html .= '<tr>';
            $html .= '<th class="casilla-cell">Casilla</th>';
            $html .= '<th>Número</th>';
            $html .= '<th>Nombre Candidato y Cargo Postulado</th>';
            $html .= '<th class="votar-cell">Votar</th>';
            $html .= '</tr>';

            $numeroCasilla = 1;

            foreach ($boletasFrente as $boleta) {
                $html .= '<tr>';
                // Columna "Casilla" como una sola celda para todas las filas
                if ($numeroCasilla === 1) {
                    $html .= '<td rowspan="' . count($boletasFrente) . '" class="casilla-cell">' . $numeroCasillaPrincipal. '</td>';
                }
                $html .= '<td>' . $numeroCasilla . '</td>';
                $html .= '<td>' . $boleta->NOMBRE_CANDIDATO . ' - ' . $boleta->CARGO_POSTULADO . '</td>';
                // Columna "Votar" como una sola celda para todas las filas
                if ($numeroCasilla === 1) {
                    $html .= '<td rowspan="' . count($boletasFrente) . '" class="votar-cell"><div class="checkbox"></div></td>';
                }
                $html .= '</tr>';

                $numeroCasilla++;
            }
            $numeroCasillaPrincipal++;

            $html .= '</table>';
        }

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


}

