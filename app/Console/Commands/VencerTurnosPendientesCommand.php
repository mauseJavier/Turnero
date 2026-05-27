<?php

namespace App\Console\Commands;

use App\Enums\TurnoEstado;
use App\Models\Turno;
use Illuminate\Console\Command;

class VencerTurnosPendientesCommand extends Command
{
    protected $signature = 'turnos:vencer-pendientes';

    protected $description = 'Marca turnos pendientes de pago vencidos cuando supera la fecha de vencimiento';

    public function handle(): int
    {
        $count = Turno::query()
            ->where('estado', TurnoEstado::PENDIENTE_PAGO->value)
            ->whereNotNull('fecha_vencimiento_pago')
            ->where('fecha_vencimiento_pago', '<=', now())
            ->update(['estado' => TurnoEstado::VENCIDO_PAGO->value]);

        $this->info("Turnos vencidos actualizados: {$count}");

        return self::SUCCESS;
    }
}
