<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
//1.- Registar al cliente
    public function register(Request $request)
    {
        //Validacion
        $request->validate([
            'nombres' => 'required',
            'correo' => 'required|email|unique:clients,correo',
            'password' => 'required'
        ]);

        $client = new Client();
        $client->nombres = $request->nombres;
        $client->correo = $request->correo;
        // Hash::make encripta la contraseña por seguridad
        $client->password = Hash::make($request->password);
        $client->save();

        return response()->json([
            "ok" => true,
            "content" => ["id" => $client->id],
            "message" => "Cliente registrado exitosamente"
        ], 201);
    }

//2.- Login para el cliente
    public function login(Request $request)
    {
        $client = Client::where('correo', $request->correo)->first();

        if (!$client || !Hash::check($request->password, $client->password)) {
            return response()->json([
                "ok" => false,
                "message" => "Usuario o contraseña incorrectos"
            ], 401);
        }

        return response()->json([
            "ok" => true,
            "content" => [
                "id" => $client->id,
                "nombres" => $client->nombres,
                "correo" => $client->correo
            ],
            "message" => "Bienvenido"
        ], 200);
    }

//3.- Generar codigo
    public function generateCode(Request $request)
    {
        $client = Client::where('correo', $request->correo)->first();

        if (!$client) {
            return response()->json(["ok" => false, "message" => "Correo no encontrado"], 404);
        }

        $code = rand(1000, 9999);

        $verification = new VerificationCode();
        $verification->client_id = $client->id;
        $verification->codigo = $code;
        $verification->fecha_caducidad = Carbon::now()->addMinutes(5);
        $verification->save();

        return response()->json([
            "ok" => true,
            "content" => ["id" => $client->id, "codigo" => $code],
            "message" => "Código generado correctamente"
        ], 200);
    }

//4.- Validar codigo
    public function validateCode(Request $request)
    {

        $verification = VerificationCode::where('client_id', $request->id)
                        ->where('codigo', $request->codigo)
                        ->orderBy('id', 'desc')
                        ->first();

        if (!$verification) {
            return response()->json(["ok" => false, "message" => "Código inválido"], 404);
        }

        if (Carbon::now()->lessThanOrEqualTo($verification->fecha_caducidad)) {
            $minutos = Carbon::now()->diffInMinutes($verification->fecha_caducidad);
            return response()->json([
                "ok" => true,
                "message" => "Código Válido. Expira en {$minutos} minutos."
            ], 200);
        } else {
            return response()->json([
                "ok" => false,
                "message" => "El código ha expirado."
            ], 400);
        }
    }
}
