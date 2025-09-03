<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'direccion',
        'tipo_servicio',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación: Una empresa tiene muchos clientes
     */
    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    /**
     * Relación: Una empresa tiene muchos servicios
     */
    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class);
    }

    /**
     * Relación: Una empresa tiene muchos recursos
     */
    public function recursos(): HasMany
    {
        return $this->hasMany(Recurso::class);
    }

    /**
     * Relación: Una empresa tiene muchos turnos
     */
    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class);
    }
}
