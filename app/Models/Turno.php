<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Turno extends Model
{
    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'servicio_id',
        'recurso_id',
        'fecha_hora_inicio',
        'fecha_hora_fin',
        'duracion_personalizada_minutos',
        'estado',
        'observaciones',
        'precio_final',
    ];

    protected $casts = [
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_fin' => 'datetime',
        'precio_final' => 'decimal:2',
    ];

    /**
     * Relación: Un turno pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Un turno pertenece a un cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación: Un turno pertenece a un servicio
     */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    /**
     * Relación: Un turno pertenece a un recurso
     */
    public function recurso(): BelongsTo
    {
        return $this->belongsTo(Recurso::class);
    }

    /**
     * Boot method para calcular automáticamente fecha_hora_fin
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($turno) {
            // Solo calcular si no se proporciona fecha_hora_fin explícitamente
            if (! $turno->fecha_hora_fin && $turno->servicio) {
                $turno->calcularFechaHoraFin();
            }
        });
    }

    /**
     * Calcular la fecha y hora de fin basado en la duración
     */
    public function calcularFechaHoraFin(): void
    {
        if ($this->fecha_hora_inicio && $this->servicio) {
            $duracion = $this->duracion_personalizada_minutos ?? $this->servicio->duracion_minutos;
            $this->fecha_hora_fin = Carbon::parse($this->fecha_hora_inicio)->addMinutes($duracion);
        }
    }

    /**
     * Obtener la duración efectiva del turno en minutos
     */
    public function getDuracionEfectivaAttribute(): int
    {
        if ($this->duracion_personalizada_minutos) {
            return $this->duracion_personalizada_minutos;
        }

        if ($this->servicio) {
            return $this->servicio->duracion_minutos;
        }

        // Fallback si no hay servicio cargado
        $servicio = Servicio::find($this->servicio_id);

        return $servicio ? $servicio->duracion_minutos : 60; // 60 minutos por defecto
    }

    /**
     * Verificar si el turno se superpone con otro
     */
    public function seSuperponeCon($fechaInicio, $fechaFin): bool
    {
        return $this->fecha_hora_inicio < $fechaFin && $this->fecha_hora_fin > $fechaInicio;
    }

    /**
     * Scopes para consultas comunes
     */
    public function scopeConfirmados($query)
    {
        return $query->where('estado', 'confirmado');
    }

    public function scopeEnFecha($query, $fecha)
    {
        return $query->whereDate('fecha_hora_inicio', $fecha);
    }

    public function scopeParaRecurso($query, $recursoId)
    {
        return $query->where('recurso_id', $recursoId);
    }
}
