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
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresa->servicios as $servicio)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800">
                            <td>{{ $servicio->nombre }}</td>
                            <td>{{ $servicio->descripcion }}</td>
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
                <button type="submit" class="w-full bg-accent text-white dark:bg-zinc-700 dark:text-white font-semibold py-1 px-2 rounded text-sm">Agregar Recurso</button>
            </form>
        </div>
    </div>
</div>
