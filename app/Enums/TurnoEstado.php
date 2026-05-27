<?php

namespace App\Enums;

enum TurnoEstado: string
{
    case PENDIENTE_PAGO = 'pendiente_pago';
    case CONFIRMADO = 'confirmado';
    case CANCELADO = 'cancelado';
    case COMPLETADO = 'completado';
    case VENCIDO_PAGO = 'vencido_pago';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
