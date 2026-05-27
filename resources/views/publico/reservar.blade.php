@extends('layouts.publico')

@section('content')
    <div class="mx-auto max-w-3xl rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <h1 class="text-2xl font-bold">Reservar turno en {{ $empresa->nombre }}</h1>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Completa tus datos y te llevamos al pago para confirmar el turno.</p>

        <form action="{{ route('publico.reserva.store', $empresa) }}" method="POST" class="mt-6 grid gap-4">
            @csrf
            <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" />
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium">Nombre</label>
                    <input name="nombre" value="{{ old('nombre') }}" required class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Apellido</label>
                    <input name="apellido" value="{{ old('apellido') }}" required class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800" />
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium">Telefono</label>
                    <input name="telefono" value="{{ old('telefono') }}" required class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Email (opcional)</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800" />
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Servicio</label>
                <select name="servicio_id" required class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800">
                    <option value="">Selecciona un servicio</option>
                    @foreach($servicios as $servicio)
                        <option value="{{ $servicio->id }}" @selected(old('servicio_id') == $servicio->id)>
                            {{ $servicio->nombre }} ({{ $servicio->duracion_minutos }} min)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Recurso</label>
                <select name="recurso_id" required class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800">
                    <option value="">Selecciona un recurso</option>
                    @foreach($empresa->recursos()->where('activo', true)->orderBy('nombre')->get() as $recurso)
                        <option value="{{ $recurso->id }}" @selected(old('recurso_id') == $recurso->id)>{{ $recurso->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Fecha y hora</label>
                <input name="fecha_hora_inicio" type="datetime-local" value="{{ old('fecha_hora_inicio') }}" required class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800" />
            </div>

            @if ($errors->any())
                <div class="rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-700 dark:border-red-700 dark:bg-red-950 dark:text-red-400">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit" class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                Confirmar y pagar
            </button>
        </form>
    </div>
@endsection
