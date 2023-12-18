<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcesoElectoral;
use App\Models\Poblacion;
use App\Models\Mesas;
use App\Models\Jurado;
use App\Notifications\NotificacionModelo;


class JuradoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jurados = Jurado::with('poblacion')->get();

        return response()->json(['data' => $jurados]);
    }

    public function store(Request $request, $cod_mesa)
    {   
        $poblacion = Poblacion::all();
        $mesa=Mesas::find($cod_mesa)->NUM_MESA;
    
        [$docentes, $estudiantes] = $poblacion->partition(function ($usuario) {
        return $usuario->DOCENTE;
        });
        
        //para presidente de mesa
        $presidenteMesa = $docentes->random();
        $this->crearJurado($presidenteMesa, $cod_mesa, "PRESIDENTE DE MESA", $mesa);

        //docentes(titulares y suplentes)
        $docentesTitulares = $docentes->random(2);
        $docentesSuplentes = $docentes->reject(function ($docente) use ($docentesTitulares) {
            return $docentesTitulares -> contains('CODSIS', $docente->CODSIS);
        })->random(2);

        foreach ($docentesTitulares as $docente) {
            $this->crearJurado($docente, $cod_mesa, "DOCENTE TITULAR", $mesa);
        }

        foreach ($docentesSuplentes as $docente) {
            $this->crearJurado($docente, $cod_mesa, "DOCENTE SUPLENTE", $mesa);
        }

        //para estudiantes(titulares y suplentes)
        $estudiantesTitulares = $estudiantes->random(2);
        $estudiantesSuplentes = $estudiantes->reject(function ($estudiante) use ($estudiantesTitulares) {
            return $estudiantesTitulares->contains('CODSIS', $estudiante->CODSIS);
        })->random(2);

        foreach ($estudiantesTitulares as $estudiante) {
            $this->crearJurado($estudiante, $cod_mesa, "ESTUDIANTE TITULAR", $mesa);
        }

        foreach ($estudiantesSuplentes as $estudiante) {
            $this->crearJurado($estudiante, $cod_mesa, "ESTUDIANTE SUPLENTE", $mesa);
        }

        return response()->json(['message' => 'Datos registrados en la tabla Jurados']);
}

    private function crearJurado($persona, $cod_mesa, $cargo)
{
    $existeJurado = Jurado::where('COD_SIS', $persona->CODSIS)
        ->where('COD_MESA', $cod_mesa)
        ->exists();

    if (!$existeJurado) {
        // Obtén la mesa y la elección asociada
        $mesa = Mesas::with('eleccion')->find($cod_mesa);

        // Asegúrate de que la mesa existe y tiene una elección asociada
        if ($mesa && $mesa->eleccion) {
            // Accede al motivo de la elección
            $motivoEleccion = $mesa->eleccion->MOTIVO_ELECCION;
            $fechaEleccion = $mesa->eleccion->FECHA_ELECCION;

            // Crea el jurado
            $jurado = new Jurado;
            $jurado->COD_SIS = $persona->CODSIS;
            $jurado->CARGO_JURADO = $cargo;
            $jurado->COD_MESA = $cod_mesa;
            $jurado->save();

            if ($persona->EMAIL != NULL) {
                $mensaje = "TRIBUNAL ELECTORAL UNIVERSITARIO informa: \n"
                    . "Usted ha sido elegido como jurado electoral\n"
                    . "como: $cargo\n"
                    . "de la mesa Nro. $cod_mesa.\n"
                    . "Con motivo de la elección de: $motivoEleccion. \n"
                    . "que se llevará a cabo la fecha de la elección: $fechaEleccion.";

                $persona->notify(new NotificacionModelo($mensaje));
            }
        }
    }
}


function store22($codMesa)
{

    // Obtén 4 docentes aleatoriamente que no estén asignados
    $docentes = Poblacion::inRandomOrder()
        ->where('DOCENTE', 1)
        ->where('CODCOMITE', null)
        ->limit(4)
        ->get();

    // Obtén 4 estudiantes aleatoriamente que no estén asignados
    $estudiantes = Poblacion::inRandomOrder()
        ->where('ESTUDIANTE', 1)
        ->where('CODCOMITE', null)
        ->limit(4)
        ->get();

    // Obtén 1 presidente de mesa aleatoriamente que no esté asignado
    $presidenteMesa = Poblacion::inRandomOrder()
        ->where('DOCENTE', 1)
        ->where('CODCOMITE', null)
        ->limit(1)
        ->get();

    // Combina docentes y estudiantes en un solo array
    $arrayJurados = $docentes->concat($estudiantes);

    // Guarda en la tabla 'jurados' a los docentes
    foreach ($arrayJurados as $jurado) {
        Jurado::create([
            'CARGO_JURADO' => 'docente', // Puedes cambiar 'docente' por el cargo que prefieras
            'COD_MESA' => $codMesa,
            'COD_SIS' => $jurado['CODSIS'],
        ]);
    }

    // Guarda en la tabla 'jurados' al presidente de mesa
    Jurado::create([
        'CARGO_JURADO' => 'presidente',
        'COD_MESA' => $codMesa,
        'COD_SIS' => $presidenteMesa->first()['CODSIS'],
    ]);

    return response()->json(['message' => 'Jurados asignados correctamente']);
}


    public function show($id)
    {
        //
    }



    public function update($carnetIdentidad)
    {
        // Buscar el correspondiente CODSIS en la tabla poblacion
        $poblacionEntry = Poblacion::where('CARNETIDENTIDAD', $carnetIdentidad)->first();

        if (!$poblacionEntry) {
            return response()->json(['error' => 'Persona not found in the poblacion table'], 404);
        }

        $codSIS = $poblacionEntry->CODSIS;

        // Buscar el jurado por CODSIS
        $jurado = Jurado::where('COD_SIS', $codSIS)->first();

        if (!$jurado) {
            return response()->json(['error' => 'Jurado not found for the given CODSIS'], 404);
        }

        // Obtener la mesa asociada al jurado
        $mesa = $jurado->mesa;

        // Obtener todos los jurados de la elección asociada a la mesa
        $jurados_de_eleccion = $mesa->eleccion->jurados;

        // Obtener población filtrada (sin los jurados de la elección actual)
        $tipoJurado = $jurado->poblacion->ESTUDIANTE ? 0 : 1;

        $poblacion = Poblacion::where($tipoJurado ? 'DOCENTE' : 'ESTUDIANTE', 1)->get();

        $poblacionFiltrada = $poblacion->reject(function ($value, $key) use ($jurados_de_eleccion, $tipoJurado) {
            return $jurados_de_eleccion && $jurados_de_eleccion->contains('COD_SIS', $value->CODSIS) && $value->$tipoJurado;
        });

        // Seleccionar un nuevo jurado aleatorio del mismo tipo
        $nuevoJurado = $poblacionFiltrada->where($tipoJurado ? 'DOCENTE' : 'ESTUDIANTE', 1)->random();

        // Eliminar el jurado actual
        $jurado->delete();

        // Verificar si el nuevo jurado ya existe en la mesa
        $existeJurado = Jurado::where('COD_SIS', $nuevoJurado->CODSIS)
            ->where('COD_MESA', $mesa->COD_MESA)
            ->exists();

        if (!$existeJurado) {
            // Crear el nuevo jurado
            $this->crearJurado($nuevoJurado, $mesa->COD_MESA, $jurado->CARGO_JURADO);
            return response()->json(['message' => 'Datos registrados en la tabla Jurados']);
        } else {
            return response()->json(['error' => 'El nuevo jurado ya existe en la mesa.']);
        }
    }


    public function update2($carnetIdentidad)
    {
        // Find the corresponding CODSIS in the poblacion table
        $poblacionEntry = Poblacion::where('CARNETIDENTIDAD', $carnetIdentidad)->first();

        if (!$poblacionEntry) {
            return response()->json(['error' => 'Persona not found in the poblacion table'], 404);
        }

        $codSIS = $poblacionEntry->CODSIS;

        // Find the jurado by CODSIS
        $jurado = Jurado::where('COD_SIS', $codSIS)->first();
        //$jurado = Jurado::find($id);
        $mesa = $jurado->mesa;
        $jurados_de_eleccion = $mesa->eleccion->jurados;

        $poblacion = $jurado->poblacion->ESTUDIANTE ? Poblacion::where('ESTUDIANTE', 1)->get() : Poblacion::where('DOCENTE', 1)->get();

        $poblacionFiltrada = $poblacion->reject(function ($value, $key) use ($jurados_de_eleccion) {
            return $jurados_de_eleccion->contains('COD_SIS', $value->CODSIS);
        });

        $nuevoJurado = $poblacionFiltrada->random();

        $jurado->delete();

        $existeJurado = Jurado::where('COD_SIS', $nuevoJurado->CODSIS)
            ->where('COD_MESA', $mesa->COD_MESA)
            ->exists();

        if (!$existeJurado) {
            $this->crearJurado($nuevoJurado, $mesa->COD_MESA, $jurado->CARGO_JURADO);
            return response()->json(['message' => 'Datos registrados en la tabla Jurados']);
        } else {

            return response()->json(['error' => 'El nuevo jurado ya existe en la mesa.']);
        }
    }


    public function obtenerJuradosPorMesa($codMesa)
{
    // Obtener la lista de jurados para la mesa especificada
    $jurados = Jurado::where('COD_MESA', $codMesa)->get();

    // Verificar si se encontraron jurados
    if ($jurados->isEmpty()) {
        $mensaje = 'No se encontraron jurados para la mesa especificada';
        return response()->json(['message' => $mensaje, 'resultados' => []]);
    }

    // Obtener la información de población para cada jurado
    $resultados = [];
    foreach ($jurados as $jurado) {
        $poblacion = Poblacion::where('CODSIS', $jurado->COD_SIS)->first();

        // Verificar si se encontró información de población
        if ($poblacion) {
            $resultados[] = [
                'COD_JURADO' => $jurado->COD_JURADO,
                'CARGO_JURADO' => $jurado->CARGO_JURADO,
                'NOMBRE' => $poblacion->NOMBRE,
                'APELLIDO' => $poblacion->APELLIDO,
                'CARNETIDENTIDAD' => $poblacion->CARNETIDENTIDAD,
                'CODSIS' => $poblacion->CODSIS,
            ];
        }
    }

    return response()->json(['resultados' => $resultados]);
}





    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    //--------------------------------------Pruebas--------------------------------------------------------------
            /*$docentes=$docentes->random(5);
        $estudiantes=$estudiantes->random(4);

        foreach ($docentes as $key=>$docente) {
            $jurado=new Jurado;
            $jurado->CARGO_JURADO="JURADO";

            if($key==0){
                $jurado->CARGO_JURADO="PRESIDENTE DE MESA";
            }

            $jurado->COD_SIS=$docente->CODSIS;
            $jurado->COD_MESA=$cod_mesa;

            $jurado->save();


            if($docente->EMAIL!=NULL){

                if($jurado->CARGO_JURADO=="JURADO"){
                    $docente->notify(new NotificacionModelo("Usted fue eligido como Jurado electoral, Mesa Nro ".$mesa));
                }else{
                    $docente->notify(new NotificacionModelo("Usted fue eligido como Jurado electoral y es Presidente de mesa, Mesa Nro ".$mesa));
                }
            }

        }

        foreach ($estudiantes as $key=>$estudiante) {
            $jurado=new Jurado;
            $jurado->CARGO_JURADO="JURADO";
            $jurado->COD_SIS=$estudiante->CODSIS;
            $jurado->COD_MESA=$cod_mesa;

            $jurado->save();

            if($estudiante->EMAIL!=NULL){
                $estudiante->notify(new NotificacionModelo("Usted fue eligido como Jurado electoral, Mesa Nro ".$mesa));
            }

        }

        return response()->json(['message' => 'Datos registrados en la tabla Jurados']);
    }*/

    /*public function store(Request $request, $cod_mesa)
    {
        $mesa = Mesas::find($cod_mesa);

        $apellidosEstudiantes = explode(',', $mesa->APELLIDOS_ESTUDIANTES);

        $estudiantes = Poblacion::whereIn('APELLIDOS', $apellidosEstudiantes)
            ->where('ESTUDIANTE', 1)
            ->get();

        if ($estudiantes->count() < 9) {
            return response()->json(['error' => 'No hay suficientes estudiantes para seleccionar jurados.']);
        }

        $juradosEstudiantes = $estudiantes->random(9);

        foreach ($juradosEstudiantes as $estudiante) {
            $this->crearJurado($estudiante, $cod_mesa, "ESTUDIANTE");
        }

        $docentes = Poblacion::where('DOCENTE', 1)->get();

        $juradosDocentes = $docentes->random(5);

        foreach ($juradosDocentes as $docente) {
            $this->crearJurado($docente, $cod_mesa, "DOCENTE");
        }

        return response()->json(['message' => 'Datos registrados en la tabla Jurados']);
    }*/

}
