<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Autenticación
 *
 * APIs para gestionar el registro, inicio de sesión y perfil de usuarios usando Laravel Sanctum.
 */
class AuthController extends Controller
{
    /**
     * Registro de nuevo usuario.
     *
     * Permite la creación de un nuevo perfil ciudadano en la plataforma. Al completarse, retorna las credenciales de acceso (Token Sanctum) en formato Bearer para autenticar el dispositivo de forma segura.
     * 
     * @unauthenticated
     *
     * @bodyParam name string required Nombre completo del ciudadano o seudónimo de registro. Example: Maria Torres
     * @bodyParam email string required Correo electrónico único válido, servirá como usuario de acceso. Example: mtorres@example.com
     * @bodyParam password string required Contraseña segura (mínimo 8 caracteres). Example: S3cur3P@ssw0rd!
     * @bodyParam password_confirmation string required Confirmación idéntica a la contraseña enviada en `password`. Example: S3cur3P@ssw0rd!
     * 
     * @response {
     *  "access_token": "1|abcdef1234567890",
     *  "token_type": "Bearer",
     *  "user": {
     *    "id": 145,
     *    "name": "Maria Torres",
     *    "email": "mtorres@example.com",
     *    "role": "user"
     *  }
     * }
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'consent_at' => now(), // Assuming consent is checked in frontend
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * Inicio de sesión.
     *
     * Autentica a un usuario existente mediante sus credenciales (correo electrónico y contraseña). El sistema emite un token de acceso temporal mediante Laravel Sanctum, que el dispositivo móvil o frontend enviará en cabeceras HTTP subsecuentes.
     * 
     * @unauthenticated
     *
     * @bodyParam email string required Correo registrado del usuario. Example: juanperez@example.com
     * @bodyParam password string required Contraseña vinculada a su perfil de seguridad. Example: M1P4ssw0rd!
     * 
     * @response {
     *  "access_token": "2|qwerty0987654321",
     *  "token_type": "Bearer",
     *  "user": {
     *    "id": 12,
     *    "name": "Juan Perez",
     *    "email": "juanperez@example.com",
     *    "role": "user"
     *  }
     * }
     * @response 422 {
     *  "message": "Las credenciales proporcionadas son incorrectas.",
     *  "errors": {
     *    "email": ["Las credenciales proporcionadas son incorrectas."]
     *  }
     * }
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * Cerrar sesión.
     *
     * Invalida el token actual (Bearer Token) del usuario, finalizando su sesión activa en el dispositivo invocador. Mejora la seguridad tras desvincular un cliente específico.
     * 
     * @authenticated
     * 
     * @response {
     *   "message": "Sesión cerrada correctamente"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    /**
     * Obtener perfil autenticado.
     *
     * Retorna la información en detalle asociada al titular del token Sanctum actual, confirmando que la sesión siga activa y las credenciales sean correctas. 
     * 
     * @authenticated
     * 
     * @response {
     *   "id": 12,
     *   "name": "Juan Perez",
     *   "email": "juanperez@example.com",
     *   "email_verified_at": null,
     *   "role": "user",
     *   "created_at": "2026-03-01T21:00:00.000000Z"
     * }
     */
    public function user(Request $request)
    {
        return $request->user();
    }
}
