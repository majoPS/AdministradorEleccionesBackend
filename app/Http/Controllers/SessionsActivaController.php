<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SessionsActiva;
use Illuminate\Support\Str;

class SessionsActivaController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validar los datos del formulario si es necesario
            $request->validate([
                // Aquí coloca tus reglas de validación según tus necesidades
            ]);

            // Crear una nueva sesión activa en la base de datos
            $session = new SessionsActiva();

            // Generar un UUID predeterminado si no se proporciona uno
            $session->id = $request->input('id', Str::uuid());

            // Utilizar el método fill para asignar automáticamente los valores del modelo
            $session->fill($request->all());

            // Guardar la sesión activa
            $session->save();

            // Puedes realizar otras acciones después de crear la sesión activa, si es necesario

            return response()->json(['message' => 'Sesión activa creada con éxito']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la sesión activa'], 500);
        }
    }
}



