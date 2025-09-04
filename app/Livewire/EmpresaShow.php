<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Empresa;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Recurso;


class EmpresaShow extends Component
{
    public Empresa $empresa;
    public $cliente_nombre;
    public $cliente_apellido;
    public $cliente_email;
    public $cliente_telefono;
    public $cliente_documento;
    public $cliente_fecha_nacimiento;
    public $cliente_observaciones;
    public $cliente_activo = true;

    public $servicio_nombre;
    public $servicio_descripcion;
    public $recurso_nombre;
    public $recurso_tipo;

    protected $rules = [
        'cliente_nombre' => 'required|string|max:255',
        'cliente_apellido' => 'required|string|max:255',
        'cliente_email' => 'required|email|max:255',
        'cliente_telefono' => 'nullable|string|max:20',
        'cliente_documento' => 'nullable|string|max:50',
        'cliente_fecha_nacimiento' => 'nullable|date',
        'cliente_observaciones' => 'nullable|string|max:1000',
        'cliente_activo' => 'boolean',
        'servicio_nombre' => 'nullable|string|max:255',
        'servicio_descripcion' => 'nullable|string|max:1000',
        'recurso_nombre' => 'nullable|string|max:255',
        'recurso_tipo' => 'nullable|string|max:255',
    ];

    public function mount(Empresa $empresa)
    {
        $this->empresa = $empresa;
    }

    public function addCliente()
    {
        $this->validate([
            'cliente_nombre' => 'required|string|max:255',
            'cliente_apellido' => 'required|string|max:255',
            'cliente_email' => 'required|email|max:255',
            'cliente_telefono' => 'nullable|string|max:20',
            'cliente_documento' => 'nullable|string|max:50',
            'cliente_fecha_nacimiento' => 'nullable|date',
            'cliente_observaciones' => 'nullable|string|max:1000',
            'cliente_activo' => 'boolean',
        ]);
        $this->empresa->clientes()->create([
            'nombre' => $this->cliente_nombre,
            'apellido' => $this->cliente_apellido,
            'email' => $this->cliente_email,
            'telefono' => $this->cliente_telefono,
            'documento' => $this->cliente_documento,
            'fecha_nacimiento' => $this->cliente_fecha_nacimiento,
            'observaciones' => $this->cliente_observaciones,
            'activo' => $this->cliente_activo,
        ]);
        $this->reset([
            'cliente_nombre',
            'cliente_apellido',
            'cliente_email',
            'cliente_telefono',
            'cliente_documento',
            'cliente_fecha_nacimiento',
            'cliente_observaciones',
            'cliente_activo',
        ]);
        $this->cliente_activo = true;
        session()->flash('success_cliente', 'Cliente agregado correctamente.');
    }

    public function addServicio()
    {
        $this->validateOnly('servicio_nombre');
        if ($this->servicio_nombre) {
            $this->empresa->servicios()->create([
                'nombre' => $this->servicio_nombre,
                'descripcion' => $this->servicio_descripcion,
            ]);
            $this->reset(['servicio_nombre', 'servicio_descripcion']);
            session()->flash('success_servicio', 'Servicio agregado correctamente.');
        }
    }

    public function addRecurso()
    {
        $this->validateOnly('recurso_nombre');
        if ($this->recurso_nombre) {
            $this->empresa->recursos()->create([
                'nombre' => $this->recurso_nombre,
                'tipo' => $this->recurso_tipo,
            ]);
            $this->reset(['recurso_nombre', 'recurso_tipo']);
            session()->flash('success_recurso', 'Recurso agregado correctamente.');
        }
    }

    public function render()
    {
        $empresa = $this->empresa->fresh(['clientes', 'servicios', 'recursos']);
        return view('livewire.empresa-show', compact('empresa'));
    }
}
