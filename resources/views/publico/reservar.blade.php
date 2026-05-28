@extends('layouts.publico')

@section('content')
    <flux:card class="mx-auto max-w-3xl p-6">
        <flux:heading size="lg">Reservar turno en {{ $empresa->nombre }}</flux:heading>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Completa tus datos y te llevamos al pago para confirmar el turno.</p>

        <form action="{{ route('publico.reserva.store', $empresa) }}" method="POST" class="mt-6 grid gap-4">
            @csrf
            <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" />
            <div class="grid gap-4 md:grid-cols-2">
                <flux:field>
                    <flux:label>Nombre</flux:label>
                    <flux:input name="nombre" value="{{ old('nombre') }}" required />
                </flux:field>
                <flux:field>
                    <flux:label>Apellido</flux:label>
                    <flux:input name="apellido" value="{{ old('apellido') }}" required />
                </flux:field>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:field>
                    <flux:label>Telefono</flux:label>
                    <flux:input name="telefono" value="{{ old('telefono') }}" required />
                </flux:field>
                <flux:field>
                    <flux:label>Email (opcional)</flux:label>
                    <flux:input name="email" type="email" value="{{ old('email') }}" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Servicio</flux:label>
                <flux:select name="servicio_id" required>
                    <option value="">Selecciona un servicio</option>
                    @foreach($servicios as $servicio)
                        <option value="{{ $servicio->id }}" @selected(old('servicio_id') == $servicio->id)>
                            {{ $servicio->nombre }} ({{ $servicio->duracion_minutos }} min)
                        </option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Recurso</flux:label>
                <flux:select name="recurso_id" required>
                    <option value="">Selecciona un recurso</option>
                    @foreach($empresa->recursos()->where('activo', true)->orderBy('nombre')->get() as $recurso)
                        <option value="{{ $recurso->id }}" @selected(old('recurso_id') == $recurso->id)>{{ $recurso->nombre }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Fecha y hora</flux:label>
                <flux:input name="fecha_hora_inicio" type="datetime-local" value="{{ old('fecha_hora_inicio') }}" required />
            </flux:field>

            @if ($errors->any())
                <div class="rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-700 dark:border-red-700 dark:bg-red-950 dark:text-red-400">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <flux:button type="submit" variant="primary">Confirmar y pagar</flux:button>
        </form>
    </flux:card>
@endsection
