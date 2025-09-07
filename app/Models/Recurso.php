<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recurso extends Model
{
    protected $fillable = [
        'empresa_id',
        'nombre',
        'descripcion',
        'tipo',
        'capacidad',
        'activo',
        'inicio_turno',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación: Un recurso pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Un recurso puede tener muchos turnos
     */
    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class);
    }

    /**
     * Relación: Un recurso puede ser usado por muchos servicios
     */
    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'recurso_servicio');
    }

    /**
     * Verificar si el recurso está disponible en un período de tiempo
     */
    public function estaDisponible($fechaInicio, $fechaFin, $excluirTurnoId = null): bool
    {
        // Normalize dates to ensure proper comparison
        $fechaInicio = Carbon::parse($fechaInicio)->format('Y-m-d H:i:s');
        $fechaFin = Carbon::parse($fechaFin)->format('Y-m-d H:i:s');

        $query = $this->turnos()
            ->where('estado', '!=', 'cancelado')
            ->where(function ($q) use ($fechaInicio, $fechaFin) {
                // Proper overlap logic: two intervals overlap if start1 < end2 AND end1 > start2
                $q->where('fecha_hora_inicio', '<', $fechaFin)
                    ->where('fecha_hora_fin', '>', $fechaInicio);
            });

        if ($excluirTurnoId) {
            $query->where('id', '!=', $excluirTurnoId);
        }

        return $query->count() === 0;
    }
}
