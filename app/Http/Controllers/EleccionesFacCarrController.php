<?php



// app\Http\Controllers\EleccionesFacCarrController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EleccionesFacCarr;

class EleccionesFacCarrController extends Controller
{
    // Ejemplo de método para guardar relaciones Elecciones-Facultad-Carrera
    public function store(Request $request)
    {
        $data = $request->validate([
            'COD_ELECCION' => 'required|integer',
            'COD_FACULTAD' => 'required|integer',
            'COD_CARRERA' => 'required|integer',
        ]);

        $eleccionFacCarr = EleccionesFacCarr::create($data);

        return response()->json($eleccionFacCarr, 201);
    }

    // Otros métodos como update, delete, etc.
}
