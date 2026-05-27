<?php

namespace App\Services\Turnos;

use App\Enums\TurnoEstado;
use App\Models\Empresa;
use App\Models\Turno;
use Carbon\Carbon;

class DisponibilidadService
{
    public function listarPorRecurso(Empresa $empresa, string $fecha): array
    {
        $finDia = Carbon::parse($fecha)->endOfDay();
        $resultados = [];

        foreach ($empresa->recursos()->where('activo', true)->get() as $recurso) {
            $slots = [];
            $inicioTurno = $recurso->inicio_turno
                ? Carbon::parse($fecha.' '.$recurso->inicio_turno)
                : Carbon::parse($fecha)->startOfDay();

            foreach ($recurso->servicios()->where('activo', true)->get() as $servicio) {
                $duracion = $servicio->duracion_minutos;
                $horaActual = $inicioTurno->copy();

                while ($horaActual->lessThan($finDia)) {
                    $horaFin = $horaActual->copy()->addMinutes($duracion);
                    if ($horaFin->greaterThan($finDia)) {
                        break;
                    }

                    if ($this->estaDisponible($recurso->id, $horaActual, $horaFin)) {
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

            $resultados[$recurso->nombre] = [
                'slots' => $slots,
                'cantidad_servicios_disponibles' => count($slots),
            ];
        }

        return $resultados;
    }

    public function listarPorServicio(Empresa $empresa, string $fecha, ?int $servicioId = null): array
    {
        $finDia = Carbon::parse($fecha)->endOfDay();

        $servicios = $empresa->servicios()->where('activo', true)->get();
        if ($servicioId) {
            $servicioFiltrado = $servicios->firstWhere('id', $servicioId);
            if (! $servicioFiltrado) {
                return [];
            }
            $servicios = collect([$servicioFiltrado]);
        }

        $resultados = [];
        foreach ($servicios as $servicio) {
            $slots = [];
            foreach ($servicio->recursos()->where('activo', true)->get() as $recurso) {
                $inicioTurno = $recurso->inicio_turno
                    ? Carbon::parse($fecha.' '.$recurso->inicio_turno)
                    : Carbon::parse($fecha)->startOfDay();
                $duracion = $servicio->duracion_minutos;
                $horaActual = $inicioTurno->copy();

                while ($horaActual->lessThan($finDia)) {
                    $horaFin = $horaActual->copy()->addMinutes($duracion);
                    if ($horaFin->greaterThan($finDia)) {
                        break;
                    }

                    if ($this->estaDisponible($recurso->id, $horaActual, $horaFin)) {
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
                'cantidad_recursos_disponibles' => count($slots),
            ];
        }

        return $resultados;
    }

    public function estaDisponible(int $recursoId, Carbon|string $fechaInicio, Carbon|string $fechaFin, ?int $excluirTurnoId = null): bool
    {
        $inicio = Carbon::parse($fechaInicio);
        $fin = Carbon::parse($fechaFin);

        $query = Turno::query()
            ->where('recurso_id', $recursoId)
            ->whereNotIn('estado', [TurnoEstado::CANCELADO->value, TurnoEstado::VENCIDO_PAGO->value])
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('fecha_hora_inicio', '<', $fin)
                    ->where('fecha_hora_fin', '>', $inicio);
            });

        if ($excluirTurnoId) {
            $query->where('id', '!=', $excluirTurnoId);
        }

        return ! $query->exists();
    }
}
