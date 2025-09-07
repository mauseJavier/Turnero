<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Recurso;
use App\Models\Servicio;
use App\Models\Cliente;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
            // $table->string('nombre');
            // $table->string('email')->unique();
            // $table->string('telefono')->nullable();
            // $table->text('direccion')->nullable();
            // $table->string('tipo_servicio'); // ej: "peluqueria", "cancha", "consultorio"
            // $table->text('descripcion')->nullable();
            // $table->boolean('activo')->default(true);

        $empresas = [
            ['nombre' => 'Empresa Uno', 'email' => 'empresauno@example.com','telefono' => '123456789',
                'direccion' => 'Direccion Uno',
                'tipo_servicio'=>'Cancha','descripcion' => 'Primera empresa'],
            ['nombre' => 'Empresa Dos', 'email' => 'empresados@example.com','telefono' => '987654321',
                'direccion' => 'Direccion Dos',
                'tipo_servicio'=>'Peluqueria','descripcion' => 'Segunda empresa'],
        ];

        foreach ($empresas as $empresaData) {
            $empresa = Empresa::create($empresaData);

            // Recursos y servicios específicos
            if ($empresa->tipo_servicio === 'Cancha') {
                $recursos = [
                    ['nombre' => 'Cancha de pádel', 'tipo' => 'cancha', 'capacidad' => 4],
                    ['nombre' => 'Cancha de fútbol', 'tipo' => 'cancha', 'capacidad' => 10],
                ];
                $servicios = [
                    ['nombre' => 'Alquiler de cancha de pádel', 'descripcion' => 'Juega pádel por hora', 'duracion_minutos' => 60, 'precio' => 120.0],
                    ['nombre' => 'Alquiler de cancha de fútbol', 'descripcion' => 'Juega fútbol por hora', 'duracion_minutos' => 60, 'precio' => 200.0],
                    ['nombre' => 'Clase de pádel', 'descripcion' => 'Clase grupal de pádel', 'duracion_minutos' => 90, 'precio' => 150.0],
                ];
            } elseif ($empresa->tipo_servicio === 'Peluqueria') {
                $recursos = [
                    ['nombre' => 'Silla de peluquería', 'tipo' => 'silla', 'capacidad' => 1],
                ];
                $servicios = [
                    ['nombre' => 'Corte de cabello', 'descripcion' => 'Corte profesional', 'duracion_minutos' => 30, 'precio' => 80.0],
                    ['nombre' => 'Coloración', 'descripcion' => 'Coloración de cabello', 'duracion_minutos' => 60, 'precio' => 200.0],
                    ['nombre' => 'Peinado', 'descripcion' => 'Peinado especial', 'duracion_minutos' => 45, 'precio' => 100.0],
                ];
            } else {
                $recursos = [
                    ['nombre' => 'Box de estética', 'tipo' => 'box', 'capacidad' => 1],
                ];
                $servicios = [
                    ['nombre' => 'Limpieza facial', 'descripcion' => 'Tratamiento facial', 'duracion_minutos' => 60, 'precio' => 150.0],
                    ['nombre' => 'Depilación', 'descripcion' => 'Depilación corporal', 'duracion_minutos' => 30, 'precio' => 90.0],
                    ['nombre' => 'Masajes', 'descripcion' => 'Masaje relajante', 'duracion_minutos' => 60, 'precio' => 180.0],
                ];
            }

            foreach ($recursos as $recurso) {
                Recurso::create([
                    'empresa_id' => $empresa->id,
                    'nombre' => $recurso['nombre'],
                    'tipo' => $recurso['tipo'],
                    'capacidad' => $recurso['capacidad'],
                    'activo' => true,
                    'inicio_turno' => '08:00:00',
                ]);
            }

            foreach ($servicios as $servicio) {
                Servicio::create([
                    'empresa_id' => $empresa->id,
                    'nombre' => $servicio['nombre'],
                    'descripcion' => $servicio['descripcion'],
                    'duracion_minutos' => $servicio['duracion_minutos'],
                    'precio' => $servicio['precio'],
                ]);
            }

            // Clientes
            for ($i = 1; $i <= 3; $i++) {
                Cliente::create([
                    'empresa_id' => $empresa->id,
                    'nombre' => "Cliente $i de {$empresa->nombre}",
                    'apellido' => "Apellido $i",
                    'email' => "cliente{$i}_{$empresa->id}@mail.com",
                ]);
            }
        }
    }
}
