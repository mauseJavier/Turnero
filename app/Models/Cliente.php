<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $fillable = [
        'empresa_id',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'documento',
        'fecha_nacimiento',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
    ];

    /**
     * Relación: Un cliente pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Un cliente puede tener muchos turnos
     */
    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class);
    }

    /**
     * Accessor para nombre completo
     */
        public function getNombreCompletoAttribute(): string
        {
            // Si ambos existen, los une. Si falta alguno, muestra el que esté.
            if ($this->nombre && $this->apellido) {
                return trim($this->nombre . ' ' . $this->apellido);
            }
            return $this->nombre ?? $this->apellido ?? '';
        }
    
        protected $appends = ['nombre_completo'];
}
