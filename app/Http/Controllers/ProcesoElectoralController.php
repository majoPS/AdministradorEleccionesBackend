<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcesoElectoral;
class ProcesoElectoralController extends Controller
{
    public function agregarProcesoElectoral (Request $request) {
        $procesoElectoral = new ProcesoElectoral();
        $procesoElectoral-> CODPROCESOELECTORAL = $request->CODPROCESOELECTORAL;
        //$procesoElectoral-> CODADMINISTRADOR= $request->CODADMINISTRADOR;
        $procesoElectoral-> CARGO= $request->CARGO;
        $procesoElectoral-> FECHAINICIOCONVOCATORIA= $request->FECHAINICIOCONVOCATORIA;
        $procesoElectoral-> FECHAFINCONVOCATORIA= $request->FECHAFINCONVOCATORIA;
        $procesoElectoral-> FECHAELECCIONES= $request->FECHAELECCIONES;
        $procesoElectoral-> TIPOELECCIONES= $request->TIPOELECCIONES;
        $procesoElectoral-> CONVOCATORIA= $request->CONVOCATORIA;
        $procesoElectoral-> save();
        return "se creo";
    }

    public function obtenerProcesosElectorales () {
        return ProcesoElectoral::get();
    }
}
