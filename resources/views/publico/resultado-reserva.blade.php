@extends('layouts.publico')

@section('content')
    <flux:card class="mx-auto max-w-3xl p-6">
        <flux:heading size="lg">Estado de tu reserva</flux:heading>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Empresa: {{ $turno->empresa->nombre }}</p>

        <div class="mt-4 rounded-lg bg-zinc-100 p-4 text-sm text-zinc-800 dark:bg-zinc-800 dark:text-zinc-100">
            <p><strong>Cliente:</strong> {{ $turno->cliente->nombre_completo }}</p>
            <p><strong>Servicio:</strong> {{ $turno->servicio->nombre }}</p>
            <p><strong>Recurso:</strong> {{ $turno->recurso->nombre }}</p>
            <p><strong>Inicio:</strong> {{ $turno->fecha_hora_inicio }}</p>
            <p><strong>Estado turno:</strong> {{ $turno->estado }}</p>
            <p><strong>Estado pago:</strong> {{ $turno->pago_status ?: 'pendiente' }}</p>
        </div>

        @if($turno->pago_init_point && $turno->estado === 'pendiente_pago')
            <flux:button href="{{ $turno->pago_init_point }}" variant="primary" class="mt-5">
                Ir a pagar
            </flux:button>
        @endif
    </flux:card>
@endsection
