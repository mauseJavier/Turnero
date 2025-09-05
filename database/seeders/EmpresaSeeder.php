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

            // Recursos
            for ($i = 1; $i <= 3; $i++) {
                Recurso::create([
                    'empresa_id' => $empresa->id,
                    'nombre' => "Recurso $i de {$empresa->nombre}",
                    'tipo' => 'sala',
                    'capacidad' => 10,
                    'activo' => true,
                    'inicio_turno' => '08:00:00',
                ]);
            }

            // Servicios
            for ($i = 1; $i <= 3; $i++) {
                Servicio::create([
                    'empresa_id' => $empresa->id,
                    'nombre' => "Servicio $i de {$empresa->nombre}",
                    'descripcion' => 'Descripción del servicio',
                    'duracion_minutos' => 90,
                    'precio' => 100.0,
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
