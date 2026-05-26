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
    public $recurso_inicio_turno;

    public $turno_cliente_id;
    public $turno_servicio_id;
    public $turno_recurso_id;
    public $turno_fecha_hora;

    public $turnos_disponibles = [];
        public $turnos_disponibles_por_recurso = [];

    public $turno_fecha_listar;

    public $asociar_servicio_id;
    public $asociar_recurso_id;

    public $servicio_filtro_id;



    
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
    

    public function asociarServicioRecurso()
    {
        $this->validate([
            'asociar_servicio_id' => 'required|exists:servicios,id',
            'asociar_recurso_id' => 'required|exists:recursos,id',
        ]);
        $servicio = Servicio::find($this->asociar_servicio_id);
        $recurso = Recurso::find($this->asociar_recurso_id);
        if ($servicio && $recurso) {
            $servicio->recursos()->syncWithoutDetaching([$recurso->id]);
            session()->flash('success_asociacion', 'Servicio y recurso asociados correctamente.');
        } else {
            session()->flash('success_asociacion', 'No se pudo asociar.');
        }
        $this->reset(['asociar_servicio_id', 'asociar_recurso_id']);
    }

    public function listarTurnosDisponiblesPorRecurso()
    {
        $fecha = $this->turno_fecha_listar ?: date('Y-m-d');
        $resultados = [];
        $finDia = \Carbon\Carbon::parse($fecha)->endOfDay();

        foreach ($this->empresa->recursos as $recurso) {
            $slots = [];
            $inicioTurno = $recurso->inicio_turno ? \Carbon\Carbon::parse($fecha.' '.$recurso->inicio_turno) : \Carbon\Carbon::parse($fecha)->startOfDay();
            // Solo servicios asociados a este recurso
            foreach ($recurso->servicios as $servicio) {
                $duracion = $servicio->duracion_minutos;
                $horaActual = $inicioTurno->copy();
                while ($horaActual->addMinutes(0)->lessThan($finDia)) {
                    $horaFin = $horaActual->copy()->addMinutes($duracion);
                    if ($horaFin->greaterThan($finDia)) break;
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
                            // 'data' => $servicio,
                            'recurso' => $recurso->nombre,
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

    public function listarTurnosDisponiblesPorServicio($servicioId = null)
    {
        $fecha = $this->turno_fecha_listar ?: date('Y-m-d');
        $resultados = [];
        $finDia = \Carbon\Carbon::parse($fecha)->endOfDay();

        $servicioId = $this->servicio_filtro_id ?? $servicioId;
        $servicios = $this->empresa->servicios;
        if ($servicioId) {
            $servicio = $servicios->where('id', $servicioId)->first();
            if ($servicio) {
                $servicios = collect([$servicio]);
            } else {
                $this->turnos_disponibles_por_servicio = [];
                return;
            }
        }

        foreach ($servicios as $servicio) {
            $slots = [];
            foreach ($servicio->recursos as $recurso) {
                $inicioTurno = $recurso->inicio_turno ? \Carbon\Carbon::parse($fecha.' '.$recurso->inicio_turno) : \Carbon\Carbon::parse($fecha)->startOfDay();
                $duracion = $servicio->duracion_minutos;
                $horaActual = $inicioTurno->copy();
                while ($horaActual->addMinutes(0)->lessThan($finDia)) {
                    $horaFin = $horaActual->copy()->addMinutes($duracion);
                    if ($horaFin->greaterThan($finDia)) break;
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
                            'recurso' => $recurso->nombre,
                            'inicio' => $horaActual->format('Y-m-d H:i'),
                            'fin' => $horaFin->format('Y-m-d H:i'),
                        ];
                    }
                    $horaActual->addMinutes($duracion);
                }
            }
            $resultados[$servicio->nombre] = [
                'slots' => $slots,
                'cantidad_recursos_disponibles' => count($slots)
            ];
        }
        $this->turnos_disponibles_por_servicio = $resultados;
    }


    public function getRecursosParaServicioProperty()
    {
        if ($this->turno_servicio_id) {
            $servicio = Servicio::with('recursos')->find($this->turno_servicio_id);
            return $servicio ? $servicio->recursos : collect();
        }
        return collect();
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

        // Validar que el recurso esté asociado al servicio
        if (! $servicio->recursos->contains('id', $recurso->id)) {
            session()->flash('error_turno', 'El recurso seleccionado no está asociado al servicio.');
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

    public $cliente_empresa_id;

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
            'cliente_empresa_id' => 'required|exists:empresas,id',
        ]);
        \App\Models\Cliente::create([
            'empresa_id' => $this->cliente_empresa_id,
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
            'cliente_empresa_id',
        ]);
        $this->cliente_activo = true;
        session()->flash('success_cliente', 'Cliente agregado correctamente.');
    }

    public $servicio_recurso_id;

    public function addServicio()
    {
        $this->validate([
            'servicio_nombre' => 'required|string|max:255',
            'servicio_descripcion' => 'nullable|string|max:1000',
            'servicio_duracion_minutos' => 'integer|min:1',
            'servicio_recurso_id' => 'required|exists:recursos,id',
        ]);
        $servicio = $this->empresa->servicios()->create([
            'nombre' => $this->servicio_nombre,
            'descripcion' => $this->servicio_descripcion,
            'duracion_minutos' => $this->servicio_duracion_minutos ?? 30,
        ]);
        // Asociar el servicio con el recurso en la tabla pivote
        $servicio->recursos()->attach($this->servicio_recurso_id);
        $this->reset(['servicio_nombre', 'servicio_descripcion', 'servicio_duracion_minutos', 'servicio_recurso_id']);
        session()->flash('success_servicio', 'Servicio agregado y asociado al recurso correctamente.');
    }

    public function addRecurso()
    {
        $this->validate([
            'recurso_nombre' => 'required|string|max:255',
            'recurso_tipo' => 'nullable|string|max:255',
            'recurso_inicio_turno' => 'nullable|date_format:H:i',
        ]);
        $this->empresa->recursos()->create([
            'nombre' => $this->recurso_nombre,
            'tipo' => $this->recurso_tipo,
            'inicio_turno' => $this->recurso_inicio_turno,
        ]);
        $this->reset(['recurso_nombre', 'recurso_tipo', 'recurso_inicio_turno']);
        session()->flash('success_recurso', 'Recurso agregado correctamente.');
    }

    public function render()
    {
        $empresa = $this->empresa->fresh(['clientes', 'servicios', 'recursos','turnos']);
        return view('livewire.empresa-show', compact('empresa'));
    }
}
