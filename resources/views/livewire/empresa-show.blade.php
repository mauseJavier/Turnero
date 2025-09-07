<div>


<div class="space-y-8">
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 p-6">
        <h2 class="text-xl font-bold mb-4">Datos de la Empresa</h2>
        <div><strong>Nombre:</strong> {{ $empresa->nombre }}</div>
        <div><strong>Email:</strong> {{ $empresa->email }}</div>
        <div><strong>Teléfono:</strong> {{ $empresa->telefono }}</div>
        <div><strong>Dirección:</strong> {{ $empresa->direccion }}</div>
        <div><strong>Tipo de Servicio:</strong> {{ $empresa->tipo_servicio }}</div>
        <div><strong>Descripción:</strong> {{ $empresa->descripcion }}</div>
        <div><strong>Activo:</strong> {{ $empresa->activo ? 'Sí' : 'No' }}</div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
    <!-- Formulario para asociar servicios con recursos -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 p-4 md:col-span-3 mb-6">
        <h3 class="font-semibold mb-2">Asociar Servicio y Recurso</h3>
        <form wire:submit.prevent="asociarServicioRecurso" class="space-y-2">
            @if (session()->has('success_asociacion'))
                <div class="text-green-600 text-xs">{{ session('success_asociacion') }}</div>
            @endif
            <select wire:model="asociar_servicio_id" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <option value="">Seleccione un servicio</option>
                @foreach($empresa->servicios as $servicio)
                    <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
                @endforeach
            </select>
            <select wire:model="asociar_recurso_id" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <option value="">Seleccione un recurso</option>
                @foreach($empresa->recursos as $recurso)
                    <option value="{{ $recurso->id }}">{{ $recurso->nombre }}</option>
                @endforeach
            </select>
            <button type="submit" class="w-full bg-accent text-white dark:bg-zinc-700 dark:text-white font-semibold py-1 px-2 rounded text-sm">Asociar Servicio y Recurso</button>
        </form>
    </div>
        <!-- Clientes -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 p-4">
            <h3 class="font-semibold mb-2">Clientes</h3>
            <table class="w-full text-xs mb-2">
                <thead>
                    <tr class="text-left border-b border-zinc-200 dark:border-zinc-700">
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Documento</th>
                        <th>Fecha Nac.</th>
                        <th>Activo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresa->clientes as $cliente)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800">
                            <td>{{ $cliente->nombre }}</td>
                            <td>{{ $cliente->apellido }}</td>
                            <td>{{ $cliente->email }}</td>
                            <td>{{ $cliente->telefono }}</td>
                            <td>{{ $cliente->documento }}</td>
                            <td>{{ $cliente->fecha_nacimiento }}</td>
                            <td>{{ $cliente->activo ? 'Sí' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <form wire:submit.prevent="addCliente" class="space-y-2">
                @if (session()->has('success_cliente'))
                    <div class="text-green-600 text-xs">{{ session('success_cliente') }}</div>
                @endif
                <select wire:model="cliente_empresa_id" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                    <option value="">Seleccione una empresa</option>
                    @foreach(App\Models\Empresa::all() as $empresaItem)
                        <option value="{{ $empresaItem->id }}">{{ $empresaItem->nombre }}</option>
                    @endforeach
                </select>
                <input type="text" wire:model="cliente_nombre" placeholder="Nombre" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <input type="text" wire:model="cliente_apellido" placeholder="Apellido" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <input type="email" wire:model="cliente_email" placeholder="Email" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <input type="text" wire:model="cliente_telefono" placeholder="Teléfono" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <input type="text" wire:model="cliente_documento" placeholder="Documento" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <input type="date" wire:model="cliente_fecha_nacimiento" placeholder="Fecha de Nacimiento" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <textarea wire:model="cliente_observaciones" placeholder="Observaciones" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm"></textarea>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="cliente_activo" id="cliente_activo" class="accent-accent h-4 w-4 rounded border-zinc-300 dark:border-zinc-600">
                    <label for="cliente_activo" class="text-sm">Activo</label>
                </div>
                <button type="submit" class="w-full bg-accent text-white dark:bg-zinc-700 dark:text-white font-semibold py-1 px-2 rounded text-sm">Agregar Cliente</button>
            </form>
        </div>
        <!-- Servicios -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 p-4">
            <h3 class="font-semibold mb-2">Servicios</h3>
            <table class="w-full text-xs mb-2">
                <thead>
                    <tr class="text-left border-b border-zinc-200 dark:border-zinc-700">
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Duración (minutos)</th>
                        <th>Recursos asociados</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresa->servicios as $servicio)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800">
                            <td>{{ $servicio->nombre }}</td>
                            <td>{{ $servicio->descripcion }}</td>
                            <td>{{ $servicio->duracion_minutos }}</td>
                            <td>
                                @foreach($servicio->recursos as $recurso)
                                    <span class="inline-block bg-zinc-200 dark:bg-zinc-700 rounded px-2 py-1 text-xs mr-1 mb-1">{{ $recurso->nombre }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <form wire:submit.prevent="addServicio" class="space-y-2">
                @if (session()->has('success_servicio'))
                    <div class="text-green-600 text-xs">{{ session('success_servicio') }}</div>
                @endif
                <input type="text" wire:model="servicio_nombre" placeholder="Nombre" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <input type="text" wire:model="servicio_descripcion" placeholder="Descripción" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <input type="number" wire:model="servicio_duracion_minutos" placeholder="Duración (minutos)" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <select wire:model="servicio_recurso_id" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                    <option value="">Seleccione un recurso para asociar</option>
                    @foreach($empresa->recursos as $recurso)
                        <option value="{{ $recurso->id }}">{{ $recurso->nombre }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-accent text-white dark:bg-zinc-700 dark:text-white font-semibold py-1 px-2 rounded text-sm">Agregar Servicio</button>
            </form>
        </div>
        <!-- Recursos -->
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 p-4">
            <h3 class="font-semibold mb-2">Recursos</h3>
            <table class="w-full text-xs mb-2">
                <thead>
                    <tr class="text-left border-b border-zinc-200 dark:border-zinc-700">
                        <th>Nombre</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresa->recursos as $recurso)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800">
                            <td>{{ $recurso->nombre }}</td>
                            <td>{{ $recurso->tipo }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <form wire:submit.prevent="addRecurso" class="space-y-2">
                @if (session()->has('success_recurso'))
                    <div class="text-green-600 text-xs">{{ session('success_recurso') }}</div>
                @endif
                <input type="text" wire:model="recurso_nombre" placeholder="Nombre" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <input type="text" wire:model="recurso_tipo" placeholder="Tipo" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <input type="time" wire:model="recurso_inicio_turno" placeholder="Inicio de turno (HH:MM)" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <button type="submit" class="w-full bg-accent text-white dark:bg-zinc-700 dark:text-white font-semibold py-1 px-2 rounded text-sm">Agregar Recurso</button>
            </form>
        </div>
    </div>


    <br>
    <br>
    <hr>

    {{-- // Turnos --}}


    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 p-4">
        <h3 class="font-semibold mb-2">Turnos</h3>
        <table class="w-full text-xs mb-2">
            <thead>
                <tr class="text-left border-b border-zinc-200 dark:border-zinc-700">
                    <th>Cliente</th>
                    <th>Servicio</th>
                    <th>Recurso</th>
                    <th>Fecha y Hora</th>
                    <th>Estado</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Precio</th>

                </tr>
            </thead>
            <tbody>
                @foreach($empresa->turnos as $turno)
                    <tr class="border-b border-zinc-100 dark:border-zinc-800">
                        <td>{{ $turno->cliente->nombre }} {{ $turno->cliente->apellido }}</td>
                        <td>{{ $turno->servicio->nombre }}</td>
                        <td>{{ $turno->recurso->nombre }}</td>
                        <td>{{ $turno->fecha_hora }}</td>
                        <td>{{ $turno->estado }}</td>
                        <td>{{ $turno->fecha_hora_inicio }}</td>
                        <td>{{ $turno->fecha_hora_fin }}</td>
                        <td>{{ $turno->precio }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <form wire:submit.prevent="addTurno" class="space-y-2">

            @if (session()->has('success_turno'))
                <div class="text-green-600 text-xs">{{ session('success_turno') }}</div>
            @endif

            @if (session()->has('error_turno'))
                <div class="text-red-600 text-xs">{{ session('error_turno') }}</div>
            @endif
            
    <select wire:model="turno_cliente_id" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
        <option value="">Seleccione un cliente</option>
        @foreach($empresa->clientes as $cliente)
            <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellido }}</option>
        @endforeach
            </select>
            <select wire:model.live="turno_servicio_id" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <option value="">Seleccione un servicio</option>
                @foreach($empresa->servicios as $servicio)
                    <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
                @endforeach
            </select>
            <select wire:model="turno_recurso_id" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
                <option value="">Seleccione un recurso</option>
                @foreach($this->recursosParaServicio as $recurso)
                    <option value="{{ $recurso->id }}">{{ $recurso->nombre }}</option>
                @endforeach
            </select>
            <input  type="datetime-local" wire:model="turno_fecha_hora" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
            <button type="submit" class="w-full bg-accent text-white dark:bg-zinc-700 dark:text-white font-semibold py-1 px-2 rounded text-sm">Agregar Turno</button>
        </form>
    </div>



    <br>
    <hr>
    <br>

    <!-- Listar todos los turnos disponibles del día por recurso -->
    <!-- Listar todos los turnos disponibles del día por servicio -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 p-4 mt-6">
        <h3 class="font-semibold mb-2">Turnos disponibles por servicio (día)</h3>
        <form wire:submit.prevent="listarTurnosDisponiblesPorServicio" class="space-y-2 mb-4">
            <input type="date" wire:model="turno_fecha_listar" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
            <select wire:model="servicio_filtro_id" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm mt-2">
                <option value="">Todos los servicios</option>
                @foreach($empresa->servicios as $servicio)
                    <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
                @endforeach
            </select>
            <button type="submit" class="w-full bg-accent text-white dark:bg-zinc-700 dark:text-white font-semibold py-1 px-2 rounded text-sm">Listar turnos por servicio</button>
        </form>
        @if(!empty($this->turnos_disponibles_por_servicio))
            @foreach($this->turnos_disponibles_por_servicio as $servicio => $data)
                <div class="mb-4">
                    <h4 class="font-semibold text-sm mb-1">{{ $servicio }}</h4>
                    <div class="text-xs mb-2">Cantidad de recursos disponibles: <span class="font-bold">{{ $data['cantidad_recursos_disponibles'] }}</span></div>
                    @if(!empty($data['slots']))
                        <table class="w-full text-xs mb-2">
                            <thead>
                                <tr class="text-left border-b border-zinc-200 dark:border-zinc-700">
                                    <th>Servicio</th>
                                    <th>Recurso</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['slots'] as $slot)
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                        <td>{{ $slot['servicio'] }}</td>
                                        <td>{{ $slot['recurso'] }}</td>
                                        <td>{{ $slot['inicio'] }}</td>
                                        <td>{{ $slot['fin'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-xs text-neutral-500">No hay turnos disponibles para este servicio.</div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="text-xs text-neutral-500">No hay resultados para mostrar.</div>
        @endif
    </div>
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 p-4 mt-6">
        <h3 class="font-semibold mb-2">Turnos disponibles por recurso (día)</h3>
        <form wire:submit.prevent="listarTurnosDisponiblesPorRecurso" class="space-y-2 mb-4">
            <input type="date" wire:model="turno_fecha_listar" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-2 py-1 text-sm">
            <button type="submit" class="w-full bg-accent text-white dark:bg-zinc-700 dark:text-white font-semibold py-1 px-2 rounded text-sm">Listar turnos por recurso</button>
        </form>
        @if(!empty($turnos_disponibles_por_recurso))
            @foreach($turnos_disponibles_por_recurso as $recurso => $data)
            {{-- @dump($data) --}}
                <div class="mb-4">
                    <h4 class="font-semibold text-sm mb-1">{{ $recurso }}</h4>
                    <div class="text-xs mb-2">Cantidad de servicios disponibles: <span class="font-bold">{{ $data['cantidad_servicios_disponibles'] }}</span></div>
                    @if(!empty($data['slots']))
                        <table class="w-full text-xs mb-2">
                            <thead>
                                <tr class="text-left border-b border-zinc-200 dark:border-zinc-700">
                                    <th>Servicio</th>
                                    <th>Recurso</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['slots'] as $slot)
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                        <td>{{ $slot['servicio'] }}</td>
                                        <td>{{ $slot['recurso'] }}</td>
                                        <td>{{ $slot['inicio'] }}</td>
                                        <td>{{ $slot['fin'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-xs text-neutral-500">No hay turnos disponibles para este recurso.</div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="text-xs text-neutral-500">No hay resultados para mostrar.</div>
        @endif
    </div>






</div>
