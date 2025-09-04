<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Empresa;

class EmpresaCreate extends Component
{
    public $nombre;
    public $email;
    public $telefono;
    public $direccion;
    public $tipo_servicio;
    public $descripcion;
    public $activo = true;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'email' => 'required|email|unique:empresas,email',
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:500',
        'tipo_servicio' => 'required|string|max:100',
        'descripcion' => 'nullable|string|max:1000',
        'activo' => 'boolean',
    ];

    public function save()
    {
        $this->validate();
        Empresa::create([
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'tipo_servicio' => $this->tipo_servicio,
            'descripcion' => $this->descripcion,
            'activo' => $this->activo,
        ]);
        session()->flash('success', 'Empresa creada correctamente.');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.empresa-create');
    }
}
