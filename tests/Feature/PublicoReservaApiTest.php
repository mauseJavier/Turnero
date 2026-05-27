<?php

use App\Enums\TurnoEstado;
use App\Models\Empresa;
use App\Models\Recurso;
use App\Models\Servicio;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('crea un turno pendiente de pago desde la api publica', function () {
    $empresa = Empresa::create([
        'nombre' => 'Empresa Test',
        'slug' => 'empresa-test',
        'email' => 'empresa@test.com',
        'telefono' => '123',
        'tipo_servicio' => 'peluqueria',
        'activo' => true,
    ]);

    $servicio = Servicio::create([
        'empresa_id' => $empresa->id,
        'nombre' => 'Corte',
        'duracion_minutos' => 30,
        'precio' => 1000,
        'activo' => true,
    ]);

    $recurso = Recurso::create([
        'empresa_id' => $empresa->id,
        'nombre' => 'Silla 1',
        'tipo' => 'silla',
        'activo' => true,
    ]);

    $servicio->recursos()->attach($recurso->id);

    $response = $this->postJson('/api/publico/'.$empresa->id.'/turnos', [
        'nombre' => 'Juan',
        'apellido' => 'Perez',
        'telefono' => '111111',
        'email' => 'juan@test.com',
        'servicio_id' => $servicio->id,
        'recurso_id' => $recurso->id,
        'fecha_hora_inicio' => now()->addDay()->format('Y-m-d H:i:s'),
    ]);

    $response->assertCreated();
    $response->assertJsonPath('turno.estado', TurnoEstado::PENDIENTE_PAGO->value);
});
