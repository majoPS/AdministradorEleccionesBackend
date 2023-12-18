<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrera;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras = Carrera::all();
        return response()->json($carreras);
    }

    public function getCarrerasByFacultad($cod_facultad)
{
    // Filtrar las carreras por el código de facultad proporcionado
    $carreras = Carrera::where('cod_facultad', $cod_facultad)->get();

    // Devolver las carreras encontradas
    return response()->json($carreras); // Puedes ajustar cómo se devuelve la información según las necesidades de tu aplicación
}

    // Otros métodos del controlador (store, show, update, delete) según tus necesidades
}
