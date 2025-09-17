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




Nuevo Cliente Json 

{
  "empresa_id": 1,
  "nombre": "Juan",
  "apellido": "Pérez",
  "email": "juan.perez@email.com",
  "telefono": "123456789",
  "documento": "12345678",
  "fecha_nacimiento": "1990-05-20",
  "observaciones": "Cliente frecuente",
  "activo": true
}



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
    // 1 reconocer el cliente (por telefono)
    // si no existe, crearlo solicitar datos basicos
    // si existe, obtener su id
    //1. listar servicios de una empresa que ya sabemos cual es
    //2. seleccionar un servicio
    //3. listar si turnos disponibles por servicio
    //4. seleccionar un turno
    //5confirmar un turno con un pago (esto ya lo tenemos con addTurno)


    // Buscar cliente por teléfono y devolver sus turnos
    Route::get('buscarportelefono', [ClienteController::class, 'buscarPorTelefono']);
    //ver servicios de una empresa http://localhost:1234/api/buscarportelefono

    // Mostrar todos los servicios de una empresa
    Route::get('empresas/{empresa}/servicios', [EmpresaController::class, 'servicios']);
    // http://localhost:1234/api/empresas/{empresa}/servicios

    // Listar turnos disponibles por servicio (custom)
    Route::get('turnosdisponiblesporservicio', [TurnoController::class, 'listarTurnosDisponiblesPorServicio']);
    // http://localhost:1234/api/turnosdisponiblesporservicio?servicio_id=1&fecha=2024-07-01&empresa_id=1


    // Crear turno (addTurno, custom, igual a store pero con validación de disponibilidad)
    Route::post('turnos/add', [TurnoController::class, 'addTurno']);
    // http://localhost:1234/api/turnos/add



    //utilizar el UPDATE para modificar un turno y verificar disponibilidad o CANCELAR
    //    Route::apiResource('turnos', TurnoController::class);
    //datos necesarios para modificar un turno
        //     $validated = $request->validate([
        //     'empresa_id' => 'required|exists:empresas,id',
        //     'cliente_id' => 'required|exists:clientes,id',
        //     'servicio_id' => 'required|exists:servicios,id',
        //     'recurso_id' => 'required|exists:recursos,id',
        //     'fecha_hora_inicio' => 'required|date',
        //     'duracion_personalizada_minutos' => 'nullable|integer|min:1|max:1440',
        //     'estado' => 'in:confirmado,cancelado,completado',
        //     'observaciones' => 'nullable|string|max:1000',
        //     'precio_final' => 'nullable|numeric|min:0',
        // ]);



});



// ejemplo de curl para modificar un turno cancelar o cambiar fecha/hora
// curl -X PATCH "http://localhost:1234/api/turnos/23" \
//   -H "Authorization: Bearer TU_TOKEN_AQUI" \
//   -H "Content-Type: application/json" \
//   -d '{
//     "empresa_id": 1,
//     "cliente_id": 19,
//     "servicio_id": 3,
//     "recurso_id": 2,
//     "fecha_hora_inicio": "2025-09-16 08:00",
//     "duracion_personalizada_minutos": 90,
//     "estado": "confirmado",
//     "observaciones": "Modificación de turno",
//     "precio_final": 200
//   }'


// ejemplo de curl para ver los servicios de una empresa
curl -X GET "https://turnos.llservicios.ar/api/empresas/1/servicios" \
  -H "Authorization: Bearer KlZJMs42auYO22GKxAkELq2lb37Hh2gJOrpGU3al39765772" \
  -H "Accept: application/json"

