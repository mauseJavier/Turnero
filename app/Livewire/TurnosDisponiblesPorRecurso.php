<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Empresa;
use Carbon\Carbon;
use App\Models\Turno;

class TurnosDisponiblesPorRecurso extends Component
{
    public $fecha;
    public $resultados = [];

    public function mount()
    {
        $this->fecha = now()->format('Y-m-d');
        $this->consultarTurnos();
    }

    public function updatedFecha()
    {
        $this->consultarTurnos();
    }

    public function consultarTurnos()
    {
        $user = Auth::user();
        $empresa = $user?->empresa;
        if (! $empresa || ! $this->fecha) {
            $this->resultados = [];
            return;
        }
        $finDia = Carbon::parse($this->fecha)->endOfDay();
        $resultados = [];
        foreach ($empresa->recursos as $recurso) {
            $slots = [];
            $inicioTurno = $recurso->inicio_turno ? Carbon::parse($this->fecha.' '.$recurso->inicio_turno) : Carbon::parse($this->fecha)->startOfDay();
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
        $this->resultados = $resultados;
    }

    public function render()
    {
        return view('livewire.turnos-disponibles-por-recurso');
    }
}
