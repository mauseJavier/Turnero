<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servicio;
use App\Models\Recurso;
use Illuminate\Support\Facades\DB;

class RecursoServicioSeeder extends Seeder
{
    public function run(): void
    {
        // Ejemplo: asociar servicios y recursos por tipo
        $servicios = Servicio::all();
        foreach ($servicios as $servicio) {
            if (str_contains(strtolower($servicio->nombre), 'padel')) {
                $recursos = Recurso::where('tipo', 'cancha')->where('nombre', 'like', '%padel%')->get();
            } elseif (str_contains(strtolower($servicio->nombre), 'fútbol')) {
                $recursos = Recurso::where('tipo', 'cancha')->where('nombre', 'like', '%futbol%')->get();
            } elseif (str_contains(strtolower($servicio->nombre), 'silla')) {
                $recursos = Recurso::where('tipo', 'silla')->get();
            } elseif (str_contains(strtolower($servicio->nombre), 'box')) {
                $recursos = Recurso::where('tipo', 'box')->get();
            } else {
                $recursos = Recurso::where('empresa_id', $servicio->empresa_id)->get(); // fallback
            }
            $servicio->recursos()->sync($recursos->pluck('id')->toArray());
            // Log para depuración
        }
    }
}
