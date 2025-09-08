<div>
    <div class="mb-4 flex gap-4 items-end">
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
    </div>
    <h2 class="text-lg font-bold mb-4">Turnos solicitados de la empresa</h2>
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
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No hay turnos solicitados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
