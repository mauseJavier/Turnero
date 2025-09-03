<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    protected $fillable = [
        'empresa_id',
        'nombre',
        'descripcion',
        'duracion_minutos',
        'precio',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Relación: Un servicio pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Un servicio puede tener muchos turnos
     */
    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class);
    }

    /**
     * Accessor para duración formateada
     */
    public function getDuracionFormateadaAttribute(): string
    {
        $horas = intval($this->duracion_minutos / 60);
        $minutos = $this->duracion_minutos % 60;

        if ($horas > 0) {
            return $horas.'h '.($minutos > 0 ? $minutos.'m' : '');
        }

        return $minutos.'m';
    }
}
