@extends('layouts.publico')

@section('content')
    <section class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <p class="text-xs uppercase tracking-widest text-zinc-500 dark:text-zinc-500">Turnero</p>
        <h1 class="mt-2 text-3xl font-bold">{{ $empresa->nombre }}</h1>
        <p class="mt-3 text-zinc-600 dark:text-zinc-400">{{ $empresa->descripcion ?: 'Reserva tu turno online en pocos pasos.' }}</p>
        <a href="{{ route('publico.reserva.create', $empresa) }}" class="mt-6 inline-flex rounded-lg bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">Reservar turno</a>
    </section>

    <section class="mt-8 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
        <h2 class="text-xl font-semibold">Servicios disponibles</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            @forelse($servicios as $servicio)
                <article class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                    <h3 class="font-semibold">{{ $servicio->nombre }}</h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $servicio->descripcion ?: 'Sin descripcion.' }}</p>
                    <p class="mt-2 text-sm">Duracion: {{ $servicio->duracion_minutos }} min</p>
                    <p class="text-sm">Precio: {{ $servicio->precio ? '$'.number_format($servicio->precio, 2) : 'A consultar' }}</p>
                    <a href="{{ route('publico.reserva.create', $empresa) }}" class="mt-3 inline-flex rounded-lg bg-zinc-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">Reservar</a>
                </article>
            @empty
                <p class="text-sm text-zinc-500">No hay servicios activos.</p>
            @endforelse
        </div>
    </section>
@endsection
