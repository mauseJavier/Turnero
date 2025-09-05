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
    public $servicio_duracion_minutos;
    public $recurso_nombre;
    public $recurso_tipo;

    public $turno_cliente_id;
    public $turno_servicio_id;
    public $turno_recurso_id;
    public $turno_fecha_hora;

    public $turnos_disponibles = [];
        public $turnos_disponibles_por_recurso = [];

    public $turno_fecha_listar;

    
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
        'servicio_duracion_minutos' => 'integer|min:1',
        'recurso_nombre' => 'nullable|string|max:255',
        'recurso_tipo' => 'nullable|string|max:255',
    ];
    
    public function mount(Empresa $empresa)
    {
        $this->empresa = $empresa;
        $this->turno_fecha_listar = date('Y-m-d');
    }
    

    public function listarTurnosDisponiblesPorRecurso()
    {
        $fecha = $this->turno_fecha_listar ?: date('Y-m-d');
        $resultados = [];
        $finDia = \Carbon\Carbon::parse($fecha)->endOfDay();

        foreach ($this->empresa->recursos as $recurso) {
            $slots = [];
            $inicioTurno = $recurso->inicio_turno ? \Carbon\Carbon::parse($fecha.' '.$recurso->inicio_turno) : \Carbon\Carbon::parse($fecha)->startOfDay();
            foreach ($this->empresa->servicios as $servicio) {
                $duracion = $servicio->duracion_minutos;
                $horaActual = $inicioTurno->copy();
                while ($horaActual->addMinutes(0)->lessThan($finDia)) {
                    $horaFin = $horaActual->copy()->addMinutes($duracion);
                    if ($horaFin->greaterThan($finDia)) break;
                    // Consultar en el modelo Turno si hay superposición
                    // dd($fecha, $horaActual, $horaFin);
                    $turnosSuperpuestos = \App\Models\Turno::where('recurso_id', $recurso->id)
                        ->where('estado', '!=', 'cancelado')
                        ->where(function($q) use ($horaActual, $horaFin) {
                            $q->where('fecha_hora_inicio', '<', $horaFin)
                              ->where('fecha_hora_fin', '>', $horaActual);
                        })
                        ->exists();
                    if (! $turnosSuperpuestos) {
                        $slots[] = [
                            'servicio' => $servicio->nombre,
                            'inicio' => $horaActual->format('Y-m-d H:i'),
                            'fin' => $horaFin->format('Y-m-d H:i'),
                        ];
                    }
                    $horaActual->addMinutes($duracion);
                }
            }
            $resultados[$recurso->nombre] = [
                'slots' => $slots,
                'cantidad_servicios_disponibles' => count($slots)
            ];
        }
        $this->turnos_disponibles_por_recurso = $resultados;
    }


    public function addTurno()
    {
        $this->validate([
            'turno_cliente_id' => 'required|exists:clientes,id',
            'turno_servicio_id' => 'required|exists:servicios,id',
            'turno_recurso_id' => 'required|exists:recursos,id',
            'turno_fecha_hora' => 'required|date',
        ]);

        $servicio = \App\Models\Servicio::find($this->turno_servicio_id);
        if (! $servicio) {
            session()->flash('error_turno', 'Servicio no encontrado.');
            return;
        }

        $fechaInicio = $this->turno_fecha_hora;
        $fechaFin = \Carbon\Carbon::parse($fechaInicio)->addMinutes($servicio->duracion_minutos);

        $recurso = \App\Models\Recurso::find($this->turno_recurso_id);
        if (! $recurso) {
            session()->flash('error_turno', 'Recurso no encontrado.');
            return;
        }

        if (! $recurso->estaDisponible($fechaInicio, $fechaFin)) {
            session()->flash('error_turno', 'El recurso no está disponible en ese horario.');
            return;
        }

        $turno = $this->empresa->turnos()->create([
            'cliente_id' => $this->turno_cliente_id,
            'servicio_id' => $this->turno_servicio_id,
            'recurso_id' => $this->turno_recurso_id,
            'fecha_hora_inicio' => $fechaInicio,
            'fecha_hora_fin' => $fechaFin,
            'estado' => 'pendiente',
        ]);

        $this->reset(['turno_cliente_id', 'turno_servicio_id', 'turno_recurso_id', 'turno_fecha_hora']);
        session()->flash('success_turno', 'Turno agregado correctamente.');
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
                'duracion_minutos' => $this->servicio_duracion_minutos ?? 30,
            ]);
            $this->reset(['servicio_nombre', 'servicio_descripcion', 'servicio_duracion_minutos']);
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
        $empresa = $this->empresa->fresh(['clientes', 'servicios', 'recursos','turnos']);
        return view('livewire.empresa-show', compact('empresa'));
    }
}
