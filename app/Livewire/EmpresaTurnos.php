<?php

namespace App\Livewire;

use App\Enums\TurnoEstado;
use App\Models\Recurso;
use App\Models\Servicio;
use App\Services\Turnos\DisponibilidadService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Turno;
use Carbon\Carbon;


class EmpresaTurnos extends Component
{
    public $turnos;
    public $fecha;
    public $cliente_id = null;
    public $servicio_id = null;
    public $recurso_id = null;
    public $estado = null;
    public $clientes = [];
    public $servicios = [];
    public $recursos = [];
    public array $estados = [];
    public array $nuevaFechaHora = [];

    public function mount()
    {
        $empresaId = Auth::user()->empresa_id ?? null;
        $this->clientes = $empresaId
            ? \App\Models\Cliente::where('empresa_id', $empresaId)->get()
            : collect();
        $this->servicios = $empresaId
            ? Servicio::where('empresa_id', $empresaId)->get()
            : collect();
        $this->recursos = $empresaId
            ? Recurso::where('empresa_id', $empresaId)->get()
            : collect();
        $this->estados = TurnoEstado::values();
        $this->fecha = now()->toDateString();
        $this->filtrarTurnos();
    }

    public function updatedFecha()
    {
        $this->filtrarTurnos();
    }

    public function updatedClienteId()
    {
        $this->filtrarTurnos();
    }

    public function updatedServicioId()
    {
        $this->filtrarTurnos();
    }

    public function updatedRecursoId()
    {
        $this->filtrarTurnos();
    }

    public function updatedEstado()
    {
        $this->filtrarTurnos();
    }

    public function filtrarTurnos()
    {
        $empresaId = Auth::user()->empresa_id ?? null;
        $query = Turno::where('empresa_id', $empresaId)->with(['cliente', 'servicio', 'recurso']);
        if ($this->fecha) {
            $query->enFecha($this->fecha);
        }
        if ($this->cliente_id) {
            $query->where('cliente_id', $this->cliente_id);
        }
        if ($this->servicio_id) {
            $query->where('servicio_id', $this->servicio_id);
        }
        if ($this->recurso_id) {
            $query->where('recurso_id', $this->recurso_id);
        }
        if ($this->estado) {
            $query->where('estado', $this->estado);
        }
        $this->turnos = $empresaId ? $query->get() : collect();
    }

    public function cambiarEstado(int $turnoId, string $estado): void
    {
        if (! in_array($estado, TurnoEstado::values(), true)) {
            return;
        }

        $empresaId = Auth::user()->empresa_id ?? null;
        $turno = Turno::where('empresa_id', $empresaId)->where('id', $turnoId)->first();

        if (! $turno) {
            return;
        }

        $turno->update(['estado' => $estado]);
        $this->filtrarTurnos();
    }

    public function exportarCsv()
    {
        $this->filtrarTurnos();

        $rows = $this->turnos;

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Cliente', 'Servicio', 'Recurso', 'Inicio', 'Fin', 'Estado', 'Pago', 'Origen']);

            foreach ($rows as $turno) {
                fputcsv($handle, [
                    $turno->cliente->nombre_completo ?? '-',
                    $turno->servicio->nombre ?? '-',
                    $turno->recurso->nombre ?? '-',
                    (string) $turno->fecha_hora_inicio,
                    (string) $turno->fecha_hora_fin,
                    $turno->estado,
                    $turno->pago_status ?? '-',
                    $turno->origen ?? '-',
                ]);
            }

            fclose($handle);
        }, 'turnos-'.now()->format('Ymd-His').'.csv');
    }

    public function reprogramarTurno(int $turnoId): void
    {
        $nuevaFecha = $this->nuevaFechaHora[$turnoId] ?? null;
        if (! $nuevaFecha) {
            return;
        }

        $empresaId = Auth::user()->empresa_id ?? null;
        $turno = Turno::with('servicio')
            ->where('empresa_id', $empresaId)
            ->where('id', $turnoId)
            ->first();

        if (! $turno) {
            return;
        }

        $inicio = Carbon::parse($nuevaFecha);
        if ($inicio->isPast()) {
            return;
        }

        $duracion = $turno->duracion_personalizada_minutos ?? $turno->servicio->duracion_minutos;
        $fin = $inicio->copy()->addMinutes($duracion);

        $disponible = app(DisponibilidadService::class)
            ->estaDisponible((int) $turno->recurso_id, $inicio, $fin, $turno->id);

        if (! $disponible) {
            return;
        }

        $turno->update([
            'fecha_hora_inicio' => $inicio,
            'fecha_hora_fin' => $fin,
        ]);

        unset($this->nuevaFechaHora[$turnoId]);
        $this->filtrarTurnos();
    }

    public function render()
    {
        return view('livewire.empresa-turnos');
    }
}
