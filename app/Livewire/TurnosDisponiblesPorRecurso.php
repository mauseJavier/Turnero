<?php

namespace App\Livewire;

use App\Services\Turnos\DisponibilidadService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TurnosDisponiblesPorRecurso extends Component
{
    public $fecha;
    public $resultados = [];

    public function mount()
    {
        $this->fecha = now()->format('Y-m-d');
        $this->consultarTurnos();
    }

    public function updatedFecha()
    {
        $this->consultarTurnos();
    }

    public function consultarTurnos()
    {
        $user = Auth::user();
        $empresa = $user?->empresa;
        if (! $empresa || ! $this->fecha) {
            $this->resultados = [];
            return;
        }

        $this->resultados = app(DisponibilidadService::class)->listarPorRecurso($empresa, $this->fecha);
    }

    public function render()
    {
        return view('livewire.turnos-disponibles-por-recurso');
    }
}
