<div>
    <h2 class="text-lg font-bold mb-4">Turnos disponibles por recurso</h2>
    <div class="mb-4">
        <label for="fecha" class="block text-sm font-medium">Fecha</label>
        <input type="date" id="fecha" wire:model.lazy="fecha" class="border rounded px-2 py-1">
    </div>
    <div>
        @forelse($resultados as $recurso => $info)
            <div class="mb-6">
                <h3 class="font-semibold text-accent mb-2">{{ $recurso }}</h3>
                <p class="text-xs mb-2">Servicios disponibles: {{ $info['cantidad_servicios_disponibles'] }}</p>
                <table class="table-auto w-full mb-2">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($info['slots'] as $slot)
                            <tr>
                                <td>{{ $slot['servicio'] }}</td>
                                <td>{{ $slot['inicio'] }}</td>
                                <td>{{ $slot['fin'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No hay turnos disponibles.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @empty
            <div class="text-center text-gray-500">No hay recursos o turnos disponibles para la fecha seleccionada.</div>
        @endforelse
    </div>
</div>
