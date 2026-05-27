@extends('layouts.publico')

@section('content')
    <div class="mx-auto max-w-3xl rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <h1 class="text-2xl font-bold">Estado de tu reserva</h1>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Empresa: {{ $turno->empresa->nombre }}</p>

        <div class="mt-4 rounded-lg bg-zinc-100 p-4 text-sm dark:bg-zinc-800">
            <p><strong>Cliente:</strong> {{ $turno->cliente->nombre_completo }}</p>
            <p><strong>Servicio:</strong> {{ $turno->servicio->nombre }}</p>
            <p><strong>Recurso:</strong> {{ $turno->recurso->nombre }}</p>
            <p><strong>Inicio:</strong> {{ $turno->fecha_hora_inicio }}</p>
            <p><strong>Estado turno:</strong> {{ $turno->estado }}</p>
            <p><strong>Estado pago:</strong> {{ $turno->pago_status ?: 'pendiente' }}</p>
        </div>

        @if($turno->pago_init_point && $turno->estado === 'pendiente_pago')
            <a href="{{ $turno->pago_init_point }}" class="mt-5 inline-flex rounded-lg bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                Ir a pagar
            </a>
        @endif
    </div>
@endsection
