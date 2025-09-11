<?php

namespace App\Http\Controllers;

use App\Models\Recurso;
use App\Models\Servicio;
use App\Models\Turno;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TurnoController extends Controller

{

    public function prueba(Request $request)
    {
        // return response()->json(['message' => 'API is working']);
        return response()->json($request->all());
    }
    /**
     * Listar todos los turnos disponibles del día por recurso
     * GET /api/turnos/disponibles-por-recurso?empresa_id=...&fecha=...
     */
    public function listarTurnosDisponiblesPorRecurso(Request $request)
    {
        //  return response()->json($request->all());


        if(!isset($request->fecha)) {
            return response()->json([
                'message' => 'La fecha es requerida.',
                'errors' => ['fecha' => ['Fecha requerida']],
                'formato' => 'http://localhost:1234/api/turnosdisponibles?empresa_id=1&fecha=06-09-2025',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if(!isset($request->empresa_id)) {
            return response()->json([
                'message' => 'El ID de la empresa es requerido.',
                'errors' => ['empresa_id' => ['Empresa requerida']],
                'formato' => 'http://localhost:1234/api/turnosdisponibles?empresa_id=1&fecha=06-09-2025',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'fecha' => 'required|date',
        ]);



        $empresa = \App\Models\Empresa::findOrFail($validated['empresa_id']);
        $fecha = $validated['fecha'];
        $finDia = Carbon::parse($fecha)->endOfDay();
        $resultados = [];

        foreach ($empresa->recursos as $recurso) {
            $slots = [];
            $inicioTurno = $recurso->inicio_turno ? Carbon::parse($fecha.' '.$recurso->inicio_turno) : Carbon::parse($fecha)->startOfDay();
            foreach ($recurso->servicios as $servicio) {
                $duracion = $servicio->duracion_minutos;
                $horaActual = $inicioTurno->copy();
                while ($horaActual->addMinutes(0)->lessThan($finDia)) {
                    $horaFin = $horaActual->copy()->addMinutes($duracion);
                    if ($horaFin->greaterThan($finDia)) break;
                    $turnosSuperpuestos = Turno::where('recurso_id', $recurso->id)
                        ->where('estado', '!=', 'cancelado')
                        ->where(function($q) use ($horaActual, $horaFin) {
                            $q->where('fecha_hora_inicio', '<', $horaFin)
                              ->where('fecha_hora_fin', '>', $horaActual);
                        })
                        ->exists();
                    if (! $turnosSuperpuestos) {
                        $slots[] = [
                            'servicio' => $servicio->nombre,
                            'inicio' => $horaActual->format('Y-m-d H:i'),
                            'fin' => $horaFin->format('Y-m-d H:i'),
                        ];
                    }
                    $horaActual->addMinutes($duracion);
                }
            }
            $resultados[$recurso->nombre] = [
                'slots' => $slots,
                'cantidad_servicios_disponibles' => count($slots)
            ];
        }
        return response()->json($resultados);
    }

    /**
     * Listar todos los turnos disponibles del día por servicio
     * GET /api/turnos/disponibles-por-servicio?empresa_id=...&fecha=...&servicio_id=...
     */
    public function listarTurnosDisponiblesPorServicio(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'fecha' => 'required|date',
            'servicio_id' => 'nullable|exists:servicios,id',
        ]);

        $empresa = \App\Models\Empresa::findOrFail($validated['empresa_id']);
        $fecha = $validated['fecha'];
        $finDia = Carbon::parse($fecha)->endOfDay();
        $servicioId = $validated['servicio_id'] ?? null;
        $servicios = $empresa->servicios;
        if ($servicioId) {
            $servicio = $servicios->where('id', $servicioId)->first();
            if ($servicio) {
                $servicios = collect([$servicio]);
            } else {
                return response()->json([]);
            }
        }

        $resultados = [];
        foreach ($servicios as $servicio) {
            $slots = [];
            foreach ($servicio->recursos as $recurso) {
                $inicioTurno = $recurso->inicio_turno ? Carbon::parse($fecha.' '.$recurso->inicio_turno) : Carbon::parse($fecha)->startOfDay();
                $duracion = $servicio->duracion_minutos;
                $horaActual = $inicioTurno->copy();
                while ($horaActual->addMinutes(0)->lessThan($finDia)) {
                    $horaFin = $horaActual->copy()->addMinutes($duracion);
                    if ($horaFin->greaterThan($finDia)) break;
                    $turnosSuperpuestos = Turno::where('recurso_id', $recurso->id)
                        ->where('estado', '!=', 'cancelado')
                        ->where(function($q) use ($horaActual, $horaFin) {
                            $q->where('fecha_hora_inicio', '<', $horaFin)
                              ->where('fecha_hora_fin', '>', $horaActual);
                        })
                        ->exists();
                    if (! $turnosSuperpuestos) {
                        $slots[] = [
                            'servicio_id' => $servicio->id,
                            'recurso_id' => $recurso->id,
                            'servicio' => $servicio->nombre,
                            'recurso' => $recurso->nombre,
                            'inicio' => $horaActual->format('Y-m-d H:i'),
                            'fin' => $horaFin->format('Y-m-d H:i'),
                        ];
                    }
                    $horaActual->addMinutes($duracion);
                }
            }
            $resultados[$servicio->nombre] = [
                'slots' => $slots,
                'cantidad_recursos_disponibles' => count($slots)
            ];
        }
        return response()->json($resultados);
    }

    /**
     * Crear un turno (addTurno)
     * POST /api/turnos/add
     */
    public function addTurno(Request $request)
    {
        // $validated = $request->validate([
        //     'empresa_id' => 'required|exists:empresas,id',
        //     'cliente_id' => 'required|exists:clientes,id',
        //     'servicio_id' => 'required|exists:servicios,id',
        //     'recurso_id' => 'required|exists:recursos,id',
        //     'fecha_hora_inicio' => 'required|date',
        // ]);


        try {
            $validated = $request->validate([
                'empresa_id' => 'required|exists:empresas,id',
                'cliente_id' => 'required|exists:clientes,id',
                'servicio_id' => 'required|exists:servicios,id',
                'recurso_id' => 'required|exists:recursos,id',
                'fecha_hora_inicio' => 'required|date',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación',
                'descripcion' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Modelo no encontrado',
                'descripcion' => 'Uno de los IDs proporcionados no existe en la base de datos.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error inesperado',
                'descripcion' => $e->getMessage()
            ], 500);
        }

        // Validar que el cliente pertenezca a la empresa
        $cliente = \App\Models\Cliente::where('id', $validated['cliente_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->first();
        if (! $cliente) {
            return response()->json([
                'message' => 'El cliente no existe para la empresa indicada.',
                'errors' => ['cliente_id' => ['Cliente no pertenece a la empresa']],
            ], 200);
        }

        $servicio = Servicio::where('id', $validated['servicio_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->firstOrFail();
        $recurso = Recurso::where('id', $validated['recurso_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->firstOrFail();

        $fechaInicio = $validated['fecha_hora_inicio'];
        $fechaFin = Carbon::parse($fechaInicio)->addMinutes($servicio->duracion_minutos);

        if (! $recurso->estaDisponible($fechaInicio, $fechaFin)) {
            return response()->json([
                'message' => 'El recurso no está disponible en ese horario.',
                'errors' => ['recurso_id' => ['Recurso no disponible']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $turno = Turno::create([
            'empresa_id' => $validated['empresa_id'],
            'cliente_id' => $validated['cliente_id'],
            'servicio_id' => $validated['servicio_id'],
            'recurso_id' => $validated['recurso_id'],
            'fecha_hora_inicio' => $fechaInicio,
            'fecha_hora_fin' => $fechaFin,
            'estado' => 'pendiente',
        ]);

        return response()->json($turno->load(['empresa', 'cliente', 'servicio', 'recurso']), Response::HTTP_CREATED);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Turno::with(['empresa', 'cliente', 'servicio', 'recurso']);

        if ($request->has('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->has('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->has('recurso_id')) {
            $query->where('recurso_id', $request->recurso_id);
        }

        if ($request->has('fecha')) {
            $query->whereDate('fecha_hora_inicio', $request->fecha);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        $turnos = $query->orderBy('fecha_hora_inicio')->paginate(15);

        return response()->json($turnos);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Form data for creating turno']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'cliente_id' => 'required|exists:clientes,id',
            'servicio_id' => 'required|exists:servicios,id',
            'recurso_id' => 'required|exists:recursos,id',
            'fecha_hora_inicio' => 'required|date|after:now',
            'duracion_personalizada_minutos' => 'nullable|integer|min:1|max:1440',
            'estado' => 'in:confirmado,cancelado,completado',
            'observaciones' => 'nullable|string|max:1000',
            'precio_final' => 'nullable|numeric|min:0',
        ]);

        // Verificar que el servicio pertenece a la empresa
        $servicio = Servicio::where('id', $validated['servicio_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->firstOrFail();

        // Verificar que el recurso pertenece a la empresa
        $recurso = Recurso::where('id', $validated['recurso_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->firstOrFail();

        // Calcular fecha y hora de fin
        $duracion = $validated['duracion_personalizada_minutos'] ?? $servicio->duracion_minutos;
        $fechaHoraFin = Carbon::parse($validated['fecha_hora_inicio'])->addMinutes($duracion);

        // Verificar disponibilidad del recurso
        if (! $recurso->estaDisponible($validated['fecha_hora_inicio'], $fechaHoraFin)) {
            return response()->json([
                'message' => 'El recurso no está disponible en el horario solicitado',
                'errors' => ['recurso_id' => ['Recurso no disponible']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Agregar la fecha_hora_fin calculada
        $validated['fecha_hora_fin'] = $fechaHoraFin;

        $turno = Turno::create($validated);

        return response()->json($turno->load(['empresa', 'cliente', 'servicio', 'recurso']), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Turno $turno)
    {
        $turno->load(['empresa', 'cliente', 'servicio', 'recurso']);

        return response()->json($turno);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Turno $turno)
    {
        return response()->json($turno->load(['empresa', 'cliente', 'servicio', 'recurso']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Turno $turno)
    {
        try {
            $validated = $request->validate([
                'empresa_id' => 'required|exists:empresas,id',
                'cliente_id' => 'required|exists:clientes,id',
                'servicio_id' => 'required|exists:servicios,id',
                'recurso_id' => 'required|exists:recursos,id',
                'fecha_hora_inicio' => 'required|date',
                'duracion_personalizada_minutos' => 'nullable|integer|min:1|max:1440',
                'estado' => 'in:confirmado,cancelado,completado',
                'observaciones' => 'nullable|string|max:1000',
                'precio_final' => 'nullable|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación',
                'descripcion' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Modelo no encontrado',
                'descripcion' => 'Uno de los IDs proporcionados no existe en la base de datos.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error inesperado',
                'descripcion' => $e->getMessage()
            ], 500);
        }

        // Verificar que el servicio pertenece a la empresa
        $servicio = Servicio::where('id', $validated['servicio_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->firstOrFail();

        // Verificar que el recurso pertenece a la empresa
        $recurso = Recurso::where('id', $validated['recurso_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->firstOrFail();

        // Calcular fecha y hora de fin
        $duracion = $validated['duracion_personalizada_minutos'] ?? $servicio->duracion_minutos;
        $fechaHoraFin = Carbon::parse($validated['fecha_hora_inicio'])->addMinutes($duracion);

        // Verificar disponibilidad del recurso (excluyendo el turno actual)
        if (! $recurso->estaDisponible($validated['fecha_hora_inicio'], $fechaHoraFin, $turno->id)) {
            return response()->json([
                'message' => 'El recurso no está disponible en el horario solicitado',
                'errors' => ['recurso_id' => ['Recurso no disponible']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Agregar la fecha_hora_fin calculada
        $validated['fecha_hora_fin'] = $fechaHoraFin;

        $turno->update($validated);

        return response()->json($turno->load(['empresa', 'cliente', 'servicio', 'recurso']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Turno $turno)
    {
        $turno->delete();

        return response()->json(['message' => 'Turno eliminado correctamente'], Response::HTTP_NO_CONTENT);
    }

    /**
     * Obtener turnos por fecha y recurso
     */
    public function porFechaYRecurso(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'recurso_id' => 'required|exists:recursos,id',
        ]);

        $turnos = Turno::with(['cliente', 'servicio'])
            ->whereDate('fecha_hora_inicio', $validated['fecha'])
            ->where('recurso_id', $validated['recurso_id'])
            ->where('estado', '!=', 'cancelado')
            ->orderBy('fecha_hora_inicio')
            ->get();

        return response()->json($turnos);
    }

    /**
     * Calcular duración y hora de fin para un turno
     */
    public function calcularHoraFin(Request $request)
    {
        $validated = $request->validate([
            'servicio_id' => 'required|exists:servicios,id',
            'fecha_hora_inicio' => 'required|date',
            'duracion_personalizada_minutos' => 'nullable|integer|min:1|max:1440',
        ]);

        $servicio = Servicio::findOrFail($validated['servicio_id']);
        $duracion = $validated['duracion_personalizada_minutos'] ?? $servicio->duracion_minutos;
        $fechaHoraFin = Carbon::parse($validated['fecha_hora_inicio'])->addMinutes($duracion);

        return response()->json([
            'fecha_hora_fin' => $fechaHoraFin,
            'duracion_minutos' => $duracion,
            'duracion_formateada' => $this->formatearDuracion($duracion),
        ]);
    }

    /**
     * Formatear duración en minutos a formato legible
     */
    private function formatearDuracion(int $minutos): string
    {
        $horas = intval($minutos / 60);
        $minutosRestantes = $minutos % 60;

        if ($horas > 0) {
            return $horas.'h '.($minutosRestantes > 0 ? $minutosRestantes.'m' : '');
        }

        return $minutosRestantes.'m';
    }
}
