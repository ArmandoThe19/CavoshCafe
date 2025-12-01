<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// 👇 ESTA LÍNEA ES LA QUE TE FALTA 👇
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de tu API CavoshCafe
Route::post('/cliente', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/cliente/codigo', [AuthController::class, 'generateCode']);
Route::post('/cliente/validar', [AuthController::class, 'validateCode']);
