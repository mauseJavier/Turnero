<?php

use App\Enums\TurnoEstado;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Recurso;
use App\Models\Servicio;
use App\Models\Turno;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('vence turnos pendientes de pago expirados', function () {
    $empresa = Empresa::create([
        'nombre' => 'Empresa Test',
        'slug' => 'empresa-test-2',
        'email' => 'empresa2@test.com',
        'telefono' => '123',
        'tipo_servicio' => 'peluqueria',
        'activo' => true,
    ]);

    $cliente = Cliente::create([
        'empresa_id' => $empresa->id,
        'nombre' => 'Ana',
        'apellido' => 'Lopez',
        'telefono' => '222222',
        'activo' => true,
    ]);

    $servicio = Servicio::create([
        'empresa_id' => $empresa->id,
        'nombre' => 'Color',
        'duracion_minutos' => 45,
        'precio' => 2000,
        'activo' => true,
    ]);

    $recurso = Recurso::create([
        'empresa_id' => $empresa->id,
        'nombre' => 'Silla 2',
        'tipo' => 'silla',
        'activo' => true,
    ]);

    Turno::create([
        'empresa_id' => $empresa->id,
        'cliente_id' => $cliente->id,
        'servicio_id' => $servicio->id,
        'recurso_id' => $recurso->id,
        'fecha_hora_inicio' => now()->addDay(),
        'fecha_hora_fin' => now()->addDay()->addMinutes(45),
        'estado' => TurnoEstado::PENDIENTE_PAGO->value,
        'fecha_vencimiento_pago' => now()->subMinute(),
        'origen' => 'api',
    ]);

    $this->artisan('turnos:vencer-pendientes')->assertSuccessful();

    expect(Turno::first()->estado)->toBe(TurnoEstado::VENCIDO_PAGO->value);
});
