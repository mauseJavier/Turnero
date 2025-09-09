<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Turno;


class EmpresaTurnos extends Component
{
    public $turnos;
    public $fecha;
    public $cliente_id = null;
    public $clientes = [];

    public function mount()
    {
        $empresaId = Auth::user()->empresa_id ?? null;
        $this->clientes = $empresaId
            ? \App\Models\Cliente::where('empresa_id', $empresaId)->get()
            : collect();
        $this->fecha = now()->toDateString();
        $this->filtrarTurnos();
    }

    public function updatedFecha()
    {
        $this->filtrarTurnos();
    }

    public function updatedClienteId()
    {
        $this->filtrarTurnos();
    }

    public function filtrarTurnos()
    {
        $empresaId = Auth::user()->empresa_id ?? null;
        $query = Turno::where('empresa_id', $empresaId)->with(['cliente', 'servicio', 'recurso']);
        if ($this->fecha) {
            $query->enFecha($this->fecha);
        }
        if ($this->cliente_id) {
            $query->where('cliente_id', $this->cliente_id);
        }
        $this->turnos = $empresaId ? $query->get() : collect();
    }

    public function render()
    {
        return view('livewire.empresa-turnos');
    }
}
