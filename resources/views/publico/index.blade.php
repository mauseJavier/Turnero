@extends('layouts.publico')

@section('content')
    <section class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <h1 class="text-3xl font-bold">Turnero</h1>
        <p class="mt-2 text-zinc-600 dark:text-zinc-400">Selecciona una empresa para reservar tu turno.</p>
    </section>

    <section class="mt-8 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
        <h2 class="text-xl font-semibold">Empresas</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            @forelse($empresas as $empresa)
                <a href="{{ route('publico.empresa.show', $empresa) }}"
                   class="block rounded-xl border border-zinc-200 p-4 transition hover:border-zinc-400 hover:shadow-sm dark:border-zinc-700 dark:hover:border-zinc-500">
                    <h3 class="font-semibold">{{ $empresa->nombre }}</h3>
                    @if($empresa->descripcion)
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ Str::limit($empresa->descripcion, 120) }}</p>
                    @endif
                    <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-500">Ver servicios &rarr;</p>
                </a>
            @empty
                <p class="text-sm text-zinc-500">No hay empresas disponibles.</p>
            @endforelse
        </div>
    </section>
@endsection
