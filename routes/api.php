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

    // quiero ver los permisos del usuario autenticado todo en una sola respuesta y el role que tiene
    // return $request->user()->roles; // devuelve un array de roles
    // return $request->user()->getRoleNames(); // devuelve un array de nombres de roles
    // return $request->user()->permissions; // devuelve un array de permisos
    // return $request->user()->getAllPermissions(); // devuelve un array de permisos con sus nombres
    // return $request->user()->hasRole('admin'); // devuelve true o false
    // return $request->user()->hasPermissionTo('edit users'); // devuelve true o false
    return response()->json([
        'user' => $request->user(),
        // 'usuarioRol' => $request->user()->getRoleNames(),
        // 'usuarioPermisos' => $request->user()->permissions,
        'roles' => $request->user()->getRoleNames(),
        'permissions' => $request->user()->getAllPermissions()->pluck('name'),
    ]);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // Ruta de pruebas
    Route::get('prueba', function (Request $request) {
        return response()->json(['message' => '¡Ruta de pruebas exitosa!']);
    });

});

Route::middleware(['auth:sanctum', 'can:edit users'])->group(function () {

    // Ruta de pruebas
    Route::get('prueba2', function (Request $request) {
        return response()->json(['message' => '¡Ruta de pruebas exitosa!']);
    });

});


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

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

    
    
    // Ruta adicional para verificar disponibilidad de recursos
    Route::post('recursos/{recurso}/verificar-disponibilidad', [RecursoController::class, 'verificarDisponibilidad']);
    
    //estos serian los pasos a seguir para solicitar un turno
    //1. listar servicios de una empresa que ya sabemos cual es
    //2. seleccionar un servicio
    //3. listar si turnos disponibles por servicio
    //4. seleccionar un turno
    //5confirmar un turno con un pago (esto ya lo tenemos con addTurno)


    //ver servicios de una empresa
    // Mostrar todos los servicios de una empresa
    Route::get('empresas/{empresa}/servicios', [EmpresaController::class, 'servicios']);
    
    // Listar turnos disponibles por servicio (custom)
    Route::get('turnosdisponiblesporservicio', [TurnoController::class, 'listarTurnosDisponiblesPorServicio']);
    
    // Crear turno (addTurno, custom, igual a store pero con validación de disponibilidad)
    Route::post('turnos/add', [TurnoController::class, 'addTurno']);

});






