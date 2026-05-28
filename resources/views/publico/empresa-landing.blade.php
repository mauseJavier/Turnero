@extends('layouts.publico')

@section('content')
    <flux:card class="p-8">
        <p class="text-xs uppercase tracking-widest text-zinc-500 dark:text-zinc-400">Turnero</p>
        <flux:heading size="xl" class="mt-2">{{ $empresa->nombre }}</flux:heading>
        <p class="mt-3 text-zinc-600 dark:text-zinc-400">{{ $empresa->descripcion ?: 'Reserva tu turno online en pocos pasos.' }}</p>
        <flux:button href="{{ route('publico.reserva.create', $empresa) }}" variant="primary" class="mt-6">Reservar turno</flux:button>
    </flux:card>

    <flux:card class="mt-8 p-6">
        <flux:heading>Servicios disponibles</flux:heading>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            @forelse($servicios as $servicio)
                <article class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <h3 class="font-semibold dark:text-zinc-100">{{ $servicio->nombre }}</h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $servicio->descripcion ?: 'Sin descripcion.' }}</p>
                    <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">Duracion: {{ $servicio->duracion_minutos }} min</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">Precio: {{ $servicio->precio ? '$'.number_format($servicio->precio, 2) : 'A consultar' }}</p>
                    <flux:button href="{{ route('publico.reserva.create', $empresa) }}" variant="primary" size="sm" class="mt-3">Reservar</flux:button>
                </article>
            @empty
                <p class="text-sm text-zinc-500 dark:text-zinc-400">No hay servicios activos.</p>
            @endforelse
        </div>
    </flux:card>
@endsection
