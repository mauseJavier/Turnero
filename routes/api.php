<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\RecursoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\TurnoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!

1|mwfuU6gi6i338AufqMhwlKND36mourOuYOyFYNJi2b0199c7


*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware([ 'role:admin'])->group(function () {

    // Ruta de pruebas
    Route::get('prueba', function (Request $request) {
        return response()->json(['message' => '¡Ruta de pruebas exitosa!']);
    });

});



// Rutas de recursos para la gestión de turnos
Route::apiResource('empresas', EmpresaController::class);
Route::apiResource('clientes', ClienteController::class);
Route::apiResource('servicios', ServicioController::class);
Route::apiResource('recursos', RecursoController::class);
Route::apiResource('turnos', TurnoController::class);

// Rutas adicionales para funcionalidades específicas de turnos
Route::post('turnos/calcular-hora-fin', [TurnoController::class, 'calcularHoraFin']);
Route::get('turnos/fecha-recurso', [TurnoController::class, 'porFechaYRecurso']);

// Listar turnos disponibles por recurso (custom)
Route::get('turnosdisponibles', [TurnoController::class, 'listarTurnosDisponiblesPorRecurso']);

// Crear turno (addTurno, custom, igual a store pero con validación de disponibilidad)
Route::post('turnos/add', [TurnoController::class, 'addTurno']);

// Ruta adicional para verificar disponibilidad de recursos
Route::post('recursos/{recurso}/verificar-disponibilidad', [RecursoController::class, 'verificarDisponibilidad']);



