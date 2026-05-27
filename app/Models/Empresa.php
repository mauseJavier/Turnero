<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Empresa extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'email',
        'telefono',
        'direccion',
        'tipo_servicio',
        'descripcion',
        'activo',
        'mp_access_token',
        'mp_public_key',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $empresa) {
            if (! $empresa->slug) {
                $base = Str::slug($empresa->nombre ?: 'empresa');
                $slug = $base;
                $suffix = 1;
                while (self::query()->where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$suffix;
                    $suffix++;
                }
                $empresa->slug = $slug;
            }
        });
    }

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

    /**
     * Relación: Una empresa tiene muchos usuarios
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
