<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;


use App\Models\Tribunal;
use App\Models\Poblacion;
use App\Models\Admin;

use App\Models\SessionsActiva;


use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;


use App\Notifications\NotificacionModelo;
use App\Notifications\NotificacionRecuperacionModelo;

use Illuminate\Support\Facades\Notification;

class TribunalController extends Controller
{

   /*  public function getSessionData()
    {
        $user = Auth::user();  // Obtiene el usuario autenticado

        if ($user) {
            return response()->json([
                'name' => $user->name,
                'email' => $user->email,
                // Otros datos que desees devolver a React
            ]);
        } else {
            return response()->json(['error' => 'No hay sesión activa'], 401);
        }
    } */






public function adminLogin(Request $request)
{
    $this->validate($request, [
        'email'    => 'required|email',
        'password' => 'required|min:6'
    ]);

    // Assuming the 'email' field in the 'admins' table corresponds to the email field
    $admin = Admin::where('email', $request->email)->first();

    if ($admin && Hash::check($request->password, $admin->password)) {
        // Authentication successful, save information in the 'sessions' table
        $data = [
            'id'             => Str::uuid(),
            'user_id'        => $admin->id,
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
            'payload'        => 'your-payload-here', // Adjust according to your needs
            'last_activity'  => now()->timestamp,
            'user'           => $admin->name, // Change to the appropriate field in your table
            'activo'         => true,
        ];

        // Save to the 'sessions' table
        try {
            app(SessionsActivaController::class)->store(new Request($data));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la sesión activa'], 500);
        }

        return response()->json(['message' => 'Credenciales válidas LOGIN']);
    }

    return response()->json(['message' => 'Credenciales inválidas LOGIN']);
}


    /* public function getAdminName()
{
    // Verificar si el usuario está autenticado como administrador
    if (Auth::guard('admin')->check()) {
        // Obtener el nombre del administrador de la sesión actual en la tabla 'sessions'
        $adminSession = SessionsActiva::where('user', Auth::guard('admin')->user()->name)
            ->where('activo', true)
            ->first();

        if ($adminSession) {
            return response()->json(['name' => $adminSession->user]);
        }
    }

    // Si no está autenticado o no se encuentra la sesión activa, devolver null
    return response()->json(['name' => null]);
} */



   /*  public function getAdminNameofi()
{
    // Obtener el nombre del administrador de la sesión actual en la tabla 'sessions'
    $adminSession = SessionsActiva::where('user', Auth::guard('admin')->user()->name)
        ->where('activo', true)
        ->first();

    if ($adminSession) {
        return response()->json(['name' => $adminSession->user]);
    } else {
        return response()->json(['name' => null]); // Devolver null si no se encuentra la sesión activa del administrador
    }
} */


  /*   public function getAdminName2()
    {
        // Obtener el nombre del administrador de la sesión actual
        $admin = Auth::guard('admin')->user();

        if ($admin) {
            return response()->json(['name' => $admin->name]);
        } else {
            return response()->json(['name' => null]); // Devolver null si no hay sesión de administrador
        }
    }
 */


        public function adminLogout(Request $request)
        {
            // Obtener el nombre del tribunal de la sesión actual
            $tribunalName = Auth::guard('admin')->user()->name;

            // Actualizar 'activo' a false en la tabla 'sessions' para el tribunal
            SessionsActiva::where('user', $tribunalName)
                ->where('activo', true)
                ->update(['activo' => false]);

            // Realizar el logout
            Auth::guard('tribunal')->logout();

            return response()->json(['message' => 'Logout exitoso']);
        }


        public function registrarCincoUsuarios(Request $request)
        {
            // Obtener los 5 carnets de identidad desde la solicitud
            $carnets = $request->input('carnets');

            // Obtener la información de la población según los carnets de identidad
            $poblacionData = Poblacion::whereIn('CARNETIDENTIDAD', $carnets)->get();

            $usuariosContraseñas = [];

            foreach ($poblacionData as $poblacion) {
                // Generar usuario aleatorio
                $usuario = 'user_' . strtolower(Str::random(8));

                // Generar contraseña aleatoria
                $contrasena = Str::random(12);

                // Crear un nuevo miembro del tribunal
                Tribunal::create([
                    'nombre' => $poblacion->NOMBRE,
                    'apellido' => $poblacion->APELLIDO,
                    'cod_carnet_identidad' => $poblacion->CARNETIDENTIDAD,
                    'usuario' => $usuario,
                    'password' => $contrasena, // Utiliza la contraseña generada directamente
                    'tribunalactivo' => true, // Cambia el nombre del campo según la nueva estructura
                    'usertype' => 'tribunal',
                ]);

                // Guardar el usuario y la contraseña para devolverlos
                $usuariosContraseñas[] = ['usuario' => $usuario, 'contrasena' => $contrasena];
            }

            return response()->json(['message' => 'Usuarios de tribunal registrados correctamente', 'usuariosContraseñas' => $usuariosContraseñas], 201);
        }




        public function tribunalLogin(Request $request)
        {
            // Validar la solicitud
            Log::info('Request received in tribunalLogin', $request->all());

            // Obtener las credenciales de la solicitud
            $usuario = $request->input('USUARIO');
            $contrasena = $request->input('CONTRASENA');

            // Buscar el tribunal en la tabla 'tribunals' usando el campo 'USUARIO'
            $tribunal = Tribunal::where('usuario', $usuario)->first();

            // Verificar si se encontró un tribunal y la contraseña coincide
            if ($tribunal && $contrasena == $tribunal->password) {
                // Autenticación exitosa

                // Obtener información adicional de la tabla poblacion usando COD_CARNET_IDENTIDAD
                $poblacionInfo = Poblacion::where('CARNETIDENTIDAD', $tribunal->cod_carnet_identidad)->first();

                $data = [
                    'id'             => Str::uuid(),
                    'user_id'        => $tribunal->id,
                    'ip_address'     => $request->ip(),
                    'user_agent'     => $request->userAgent(),
                    'payload'        => 'your-payload-here', // Ajusta según tus necesidades
                    'last_activity'  => now()->timestamp,
                    'user'           => $tribunal->usuario, // Cambia al campo apropiado en tu tabla
                    'activo'         => true,
                ];

                // Guardar en la tabla 'sessions'
                try {
                    app(SessionsActivaController::class)->store(new Request($data));
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Error al crear la sesión activa'], 500);
                }

                // Retornar información adicional al frontend
                return response()->json([
                    'success' => true,
                    'message' => 'Autenticación exitosa',
                    'user' => $tribunal,
                    'poblacionInfo' => $poblacionInfo,
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Credenciales inválidas']);
            }
        }




        public function obtenerTodosLosDatos()
    {
        // Obtener los datos de las tablas tribunals y poblacion mediante un join
        $datos = Tribunal::join('poblacion', 'tribunals.cod_carnet_identidad', '=', 'poblacion.CARNETIDENTIDAD')
            ->select(
                'tribunals.nombre',
                'tribunals.apellido',
                'tribunals.cod_carnet_identidad',
                'tribunals.usuario',
                'tribunals.password',
                'poblacion.CODSIS'
            )
            ->get();

        return response()->json(['datos' => $datos]);
    }

    public function recuperarUsuarioPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');

        // Busca el correo en la tabla poblacion
        $poblacion = Poblacion::where('EMAIL', $email)->first();

        if (!$poblacion) {
            return response()->json(['message' => 'Correo no encontrado en la tabla poblacion'], 404);
        }

        // Busca el usuario en la tabla tribunals usando el CARNETIDENTIDAD
        $tribunal = Tribunal::where('cod_carnet_identidad', $poblacion->CARNETIDENTIDAD)->first();

        if (!$tribunal) {
            return response()->json(['message' => 'Usuario no encontrado en la tabla tribunals'], 404);
        }

        // Envía un correo electrónico con la información de usuario y contraseña
        $data = [
            'nombre' => $tribunal->nombre,
            'usuario' => $tribunal->usuario,
            'contrasena' => $tribunal->password,
        ];

        if ($poblacion->EMAIL != NULL) {
            $mensaje = "TRIBUNAL ELECTORAL UNIVERSITARIO informa: \n"
                . "Usted ha sido elegido como jurado electoral\n"
                . "como: $tribunal->nombre\n";

            $poblacion->notify(new NotificacionModelo($mensaje));
        }

       //Notification::route('mail', $email)->notify(new NotificacionRecuperacionModelo($data['nombre'], $data['usuario'], $data['contrasena']));

        return response()->json(['message' => 'Correo enviado con éxito']);
    }


   /*  public function getUser()
    {
        // Verificar si el usuario está autenticado
        if (Auth::guard('tribunal')->check()) {
            // Obtener la información del usuario desde la sesión
            $usuario = [
                'nombre' => Session::get('poblacion_nombre'),
                'apellido' => Session::get('poblacion_apellido'),
                'cod_sis' => Session::get('poblacion_cod_sis'),
            ];

            return response()->json(['success' => true, 'usuario' => $usuario]);
        } else {
            return response()->json(['success' => false, 'message' => 'Usuario no autenticado']);
        }
    }
 */


    public function tribunalLogout(Request $request)
    {
        Auth::guard('tribunal')->logout();

        return response()->json(['message' => 'Logout exitoso']);
    }


    /* public function logout(Request $request)
    {
        Auth::logout();

        return response()->json(['success' => true, 'message' => 'Logout exitoso']);
    } */


/* public function logout(Request $request)
{
    Auth::guard('web')->logout(); // Cierra la sesión web
    $request->user()->tokens()->delete(); // Revoca todos los tokens del usuario

    return response()->json(['success' => true, 'message' => 'Logout exitoso']);
} */
/* public function getUser2(Request $request)
{
    // Obtener usuario de la sesión
    $usuario = $request->session()->get('usuario');

    if ($usuario) {
        return response()->json(['success' => true, 'usuario' => $usuario]);
    } else {
        return response()->json(['success' => false, 'message' => 'Usuario no autenticado']);
    }
} */

/* public function validarCredenciales(Request $request)
{
    // Validar la solicitud
    $request->validate([
        'USUARIO' => 'required|string',
        'CONTRASENA' => 'required|string',
    ]);

    // Obtener las credenciales de la solicitud
    $usuario = $request->input('USUARIO');
    $contrasena = $request->input('CONTRASENA');

    // Buscar el usuario en la base de datos
    $tribunal = Tribunal::where('USUARIO', $usuario)->first();

    // Verificar si el usuario existe y la contraseña es correcta
    if ($tribunal && Hash::check($contrasena, $tribunal->password)) {
        return response()->json(['success' => true, 'message' => 'Credenciales válidas']);
    } else {
        return response()->json(['success' => false, 'message' => 'Credenciales inválidas']);
    }
}


public function verificarAuthYSession()
    {
        if (Auth::check()) {
            // El usuario está autenticado

            // Accede al usuario autenticado
            $usuario = Auth::user();

            // También puedes acceder a la información de la sesión
            $sesion = session()->all();

            return response()->json([
                'auth' => true,
                'usuario' => $usuario,
                'sesion' => $sesion,
            ]);
        }

        return response()->json([
            'auth' => false,
            'mensaje' => 'No hay autenticación válida',
        ]);
    } */

}
