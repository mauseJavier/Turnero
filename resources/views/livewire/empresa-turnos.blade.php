<div>
    <div class="mb-4 grid gap-4 md:grid-cols-5 items-end">
        <div>
            <label for="fecha" class="block text-sm font-medium">Fecha</label>
            <input type="date" id="fecha" wire:model.lazy="fecha" class="border rounded px-2 py-1">
        </div>
        <div>
            <label for="cliente_id" class="block text-sm font-medium">Cliente</label>
            <select id="cliente_id" wire:model.lazy="cliente_id" class="border rounded px-2 py-1 bg-white dark:bg-gray-800 dark:text-white shadow focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="" class="text-gray-500 dark:text-gray-300">Todos</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nombre_completo }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="servicio_id" class="block text-sm font-medium">Servicio</label>
            <select id="servicio_id" wire:model.lazy="servicio_id" class="border rounded px-2 py-1 bg-white dark:bg-gray-800 dark:text-white shadow">
                <option value="">Todos</option>
                @foreach($servicios as $servicio)
                    <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="recurso_id" class="block text-sm font-medium">Recurso</label>
            <select id="recurso_id" wire:model.lazy="recurso_id" class="border rounded px-2 py-1 bg-white dark:bg-gray-800 dark:text-white shadow">
                <option value="">Todos</option>
                @foreach($recursos as $recurso)
                    <option value="{{ $recurso->id }}">{{ $recurso->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="estado" class="block text-sm font-medium">Estado</label>
            <select id="estado" wire:model.lazy="estado" class="border rounded px-2 py-1 bg-white dark:bg-gray-800 dark:text-white shadow">
                <option value="">Todos</option>
                @foreach($estados as $estadoItem)
                    <option value="{{ $estadoItem }}">{{ $estadoItem }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <h2 class="text-lg font-bold mb-4">Turnos solicitados de la empresa</h2>
    <div class="mb-3">
        <button wire:click="exportarCsv" class="rounded bg-zinc-800 px-3 py-2 text-xs font-semibold text-white">Exportar CSV</button>
    </div>
    <table class="table-auto w-full">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Servicio</th>
                <th>Recurso</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Estado</th>
                <th>Precio</th>
                <th>Pago</th>
                <th>Origen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($turnos as $turno)
                <tr>
                    <td>{{ $turno->cliente->nombre_completo ?? '-' }}</td>
                    <td>{{ $turno->servicio->nombre ?? '-' }}</td>
                    <td>{{ $turno->recurso->nombre ?? '-' }}</td>
                    <td>{{ $turno->fecha_hora_inicio }}</td>
                    <td>{{ $turno->fecha_hora_fin }}</td>
                    <td>{{ $turno->estado }}</td>
                    <td>{{ $turno->servicio->precio ? '$' . number_format($turno->servicio->precio, 2) : '-' }}</td>
                    <td>{{ $turno->pago_status ?: '-' }}</td>
                    <td>{{ $turno->origen ?: '-' }}</td>
                    <td class="space-x-1">
                        <button wire:click="cambiarEstado({{ $turno->id }}, 'confirmado')" class="rounded bg-green-600 px-2 py-1 text-xs text-white">Confirmar</button>
                        <button wire:click="cambiarEstado({{ $turno->id }}, 'cancelado')" class="rounded bg-red-600 px-2 py-1 text-xs text-white">Cancelar</button>
                        <button wire:click="cambiarEstado({{ $turno->id }}, 'completado')" class="rounded bg-blue-600 px-2 py-1 text-xs text-white">Completar</button>
                        <div class="mt-2 flex gap-1">
                            <input type="datetime-local" wire:model="nuevaFechaHora.{{ $turno->id }}" class="rounded border px-1 py-1 text-xs" />
                            <button wire:click="reprogramarTurno({{ $turno->id }})" class="rounded bg-amber-600 px-2 py-1 text-xs text-white">Reprogramar</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">No hay turnos solicitados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
