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
