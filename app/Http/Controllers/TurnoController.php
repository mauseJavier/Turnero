<?php

namespace App\Http\Controllers;

use App\Enums\TurnoEstado;
use App\Models\Recurso;
use App\Models\Servicio;
use App\Models\Turno;
use App\Services\Turnos\DisponibilidadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TurnoController extends Controller
{
    public function listarTurnosDisponiblesPorRecurso(Request $request, DisponibilidadService $disponibilidadService)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'fecha' => 'required|date',
        ]);

        $this->authorizeEmpresa((int) $validated['empresa_id']);

        $empresa = \App\Models\Empresa::findOrFail($validated['empresa_id']);

        return response()->json($disponibilidadService->listarPorRecurso($empresa, $validated['fecha']));
    }

    public function listarTurnosDisponiblesPorServicio(Request $request, DisponibilidadService $disponibilidadService)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'fecha' => 'required|date',
            'servicio_id' => 'nullable|exists:servicios,id',
        ]);

        $this->authorizeEmpresa((int) $validated['empresa_id']);

        $empresa = \App\Models\Empresa::findOrFail($validated['empresa_id']);

        return response()->json(
            $disponibilidadService->listarPorServicio(
                $empresa,
                $validated['fecha'],
                isset($validated['servicio_id']) ? (int) $validated['servicio_id'] : null
            )
        );
    }

    public function addTurno(Request $request, DisponibilidadService $disponibilidadService)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'cliente_id' => 'required|exists:clientes,id',
            'servicio_id' => 'required|exists:servicios,id',
            'recurso_id' => 'required|exists:recursos,id',
            'fecha_hora_inicio' => 'required|date|after:now',
        ]);

        return $this->crearTurno($validated, $disponibilidadService, TurnoEstado::PENDIENTE_PAGO->value);
    }

    public function index(Request $request)
    {
        $query = Turno::with(['empresa', 'cliente', 'servicio', 'recurso']);

        if ($request->filled('empresa_id')) {
            $empresaId = (int) $request->empresa_id;
            $this->authorizeEmpresa($empresaId);
            $query->where('empresa_id', $empresaId);
        } else {
            $user = auth()->user();
            if ($user && $user->hasRole('admin')) {
                $query->where('empresa_id', $user->empresa_id);
            }
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

        return response()->json($query->orderBy('fecha_hora_inicio')->paginate(15));
    }

    public function create()
    {
        return response()->json(['message' => 'Form data for creating turno']);
    }

    public function store(Request $request, DisponibilidadService $disponibilidadService)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'cliente_id' => 'required|exists:clientes,id',
            'servicio_id' => 'required|exists:servicios,id',
            'recurso_id' => 'required|exists:recursos,id',
            'fecha_hora_inicio' => 'required|date|after:now',
            'duracion_personalizada_minutos' => 'nullable|integer|min:1|max:1440',
            'estado' => 'nullable|in:'.implode(',', TurnoEstado::values()),
            'observaciones' => 'nullable|string|max:1000',
            'precio_final' => 'nullable|numeric|min:0',
        ]);

        $estado = $validated['estado'] ?? TurnoEstado::PENDIENTE_PAGO->value;

        return $this->crearTurno($validated, $disponibilidadService, $estado);
    }

    public function show(Turno $turno)
    {
        $this->authorizeEmpresa((int) $turno->empresa_id);

        return response()->json($turno->load(['empresa', 'cliente', 'servicio', 'recurso']));
    }

    public function edit(Turno $turno)
    {
        $this->authorizeEmpresa((int) $turno->empresa_id);

        return response()->json($turno->load(['empresa', 'cliente', 'servicio', 'recurso']));
    }

    public function update(Request $request, Turno $turno, DisponibilidadService $disponibilidadService)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'cliente_id' => 'required|exists:clientes,id',
            'servicio_id' => 'required|exists:servicios,id',
            'recurso_id' => 'required|exists:recursos,id',
            'fecha_hora_inicio' => 'required|date|after:now',
            'duracion_personalizada_minutos' => 'nullable|integer|min:1|max:1440',
            'estado' => 'nullable|in:'.implode(',', TurnoEstado::values()),
            'observaciones' => 'nullable|string|max:1000',
            'precio_final' => 'nullable|numeric|min:0',
        ]);

        $this->authorizeEmpresa((int) $validated['empresa_id']);

        $cliente = \App\Models\Cliente::query()
            ->where('id', $validated['cliente_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->firstOrFail();

        $servicio = Servicio::query()
            ->where('id', $validated['servicio_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->firstOrFail();

        $recurso = Recurso::query()
            ->where('id', $validated['recurso_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->firstOrFail();

        unset($cliente);

        $duracion = $validated['duracion_personalizada_minutos'] ?? $servicio->duracion_minutos;
        $fechaHoraFin = Carbon::parse($validated['fecha_hora_inicio'])->addMinutes($duracion);

        if (! $disponibilidadService->estaDisponible($recurso->id, $validated['fecha_hora_inicio'], $fechaHoraFin, $turno->id)) {
            return response()->json([
                'message' => 'El recurso no está disponible en el horario solicitado',
                'errors' => ['recurso_id' => ['Recurso no disponible']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validated['fecha_hora_fin'] = $fechaHoraFin;
        $validated['estado'] = $validated['estado'] ?? $turno->estado;

        $turno->update($validated);

        return response()->json($turno->load(['empresa', 'cliente', 'servicio', 'recurso']));
    }

    public function destroy(Turno $turno)
    {
        $this->authorizeEmpresa((int) $turno->empresa_id);
        $turno->delete();

        return response()->json(['message' => 'Turno eliminado correctamente'], Response::HTTP_NO_CONTENT);
    }

    public function porFechaYRecurso(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'recurso_id' => 'required|exists:recursos,id',
        ]);

        $recurso = Recurso::findOrFail($validated['recurso_id']);
        $this->authorizeEmpresa((int) $recurso->empresa_id);

        $turnos = Turno::with(['cliente', 'servicio'])
            ->whereDate('fecha_hora_inicio', $validated['fecha'])
            ->where('recurso_id', $validated['recurso_id'])
            ->whereNotIn('estado', [TurnoEstado::CANCELADO->value, TurnoEstado::VENCIDO_PAGO->value])
            ->orderBy('fecha_hora_inicio')
            ->get();

        return response()->json($turnos);
    }

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

    private function crearTurno(array $validated, DisponibilidadService $disponibilidadService, string $estado)
    {
        $this->authorizeEmpresa((int) $validated['empresa_id']);

        $cliente = \App\Models\Cliente::query()
            ->where('id', $validated['cliente_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->first();

        if (! $cliente) {
            return response()->json([
                'message' => 'El cliente no existe para la empresa indicada.',
                'errors' => ['cliente_id' => ['Cliente no pertenece a la empresa']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $servicio = Servicio::where('id', $validated['servicio_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->firstOrFail();

        $recurso = Recurso::where('id', $validated['recurso_id'])
            ->where('empresa_id', $validated['empresa_id'])
            ->firstOrFail();

        $duracion = $validated['duracion_personalizada_minutos'] ?? $servicio->duracion_minutos;
        $fechaInicio = Carbon::parse($validated['fecha_hora_inicio']);
        $fechaFin = $fechaInicio->copy()->addMinutes($duracion);

        if (! $disponibilidadService->estaDisponible($recurso->id, $fechaInicio, $fechaFin)) {
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
            'duracion_personalizada_minutos' => $validated['duracion_personalizada_minutos'] ?? null,
            'estado' => $estado,
            'observaciones' => $validated['observaciones'] ?? null,
            'precio_final' => $validated['precio_final'] ?? $servicio->precio,
            'origen' => 'api',
        ]);

        return response()->json($turno->load(['empresa', 'cliente', 'servicio', 'recurso']), Response::HTTP_CREATED);
    }

    private function formatearDuracion(int $minutos): string
    {
        $horas = intdiv($minutos, 60);
        $minutosRestantes = $minutos % 60;

        if ($horas > 0) {
            return $horas.'h '.($minutosRestantes > 0 ? $minutosRestantes.'m' : '');
        }

        return $minutosRestantes.'m';
    }

    private function authorizeEmpresa(int $empresaId): void
    {
        $user = auth()->user();
        if (! $user || $user->hasRole('super')) {
            return;
        }

        abort_unless((int) $user->empresa_id === $empresaId, 403, 'No autorizado para esta empresa');
    }
}
