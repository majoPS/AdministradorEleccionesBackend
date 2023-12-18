<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrador;

class AdministradorController extends Controller
{
    public function index()
    {
        $administradores = Administrador::all();
        return response()->json($administradores);    }


        public function verificarAdministrador($name)
        {
            // Realiza la verificación del administrador y devuelve los datos en formato JSON
            $administrador = Administrador::where('nombreadministrador', $name)->first();
    
            if ($administrador) {
                return response()->json($administrador);
            } else {
                return response()->json([], 404);
            }
        }



    public function create()
    {
        return view('administradores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'CODCOMITE' => 'required',
            'nombreadministrador' => 'required',
            'contrasenaadministrador' => 'required',
            'correo' => 'required|email',
            'telefono' => 'required',
        ]);

        Administrador::create($request->all());

        return redirect()->route('administradores.index')
            ->with('success', 'Administrador creado exitosamente');
    }

    public function edit(Administrador $administrador)
    {
        return view('administradores.edit', compact('administrador'));
    }

    public function update(Request $request, Administrador $administrador)
    {
        $request->validate([
            'CODCOMITE' => 'required',
            'nombreadministrador' => 'required',
            'contrasenaadministrador' => 'required',
            'correo' => 'required|email',
            'telefono' => 'required',
        ]);

        $administrador->update($request->all());

        return redirect()->route('administradores.index')
            ->with('success', 'Administrador actualizado exitosamente');
    }

    public function destroy(Administrador $administrador)
    {
        $administrador->delete();

        return redirect()->route('administradores.index')
            ->with('success', 'Administrador eliminado exitosamente');
    }
}
