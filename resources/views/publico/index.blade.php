@extends('layouts.publico')

@section('content')


    <flux:card class="p-8 space-y-6">
        <div class="space-y-1">
            <flux:text variant="subtle" class="text-xs uppercase tracking-widest">Turnero</flux:text>
            <flux:heading size="xl">
                Reservá tu turno<br>
                <span class="text-zinc-500 dark:text-zinc-400">en segundos</span>
            </flux:heading>
        </div>
        <flux:text class="max-w-md">
            Encontrá la empresa que necesitás, elegí servicio y horario, y confirmá tu turno sin vueltas.
        </flux:text>
        <div class="flex flex-wrap gap-3">
            <flux:button href="#empresas" variant="primary">Ver empresas</flux:button>
            @guest
                <flux:button href="{{ route('register') }}" variant="ghost">Crear cuenta</flux:button>
            @endguest
        </div>
    </flux:card>

    @if($stats['empresas'] > 0)
        <section class="mt-8 grid gap-4 sm:grid-cols-3">
            <flux:card size="sm" class="text-center space-y-1">
                <flux:heading size="xl" class="!text-2xl">{{ $stats['empresas'] }}</flux:heading>
                <flux:text variant="subtle" class="text-xs">empresas activas</flux:text>
            </flux:card>
            <flux:card size="sm" class="text-center space-y-1">
                <flux:heading size="xl" class="!text-2xl">{{ $stats['servicios'] }}</flux:heading>
                <flux:text variant="subtle" class="text-xs">servicios disponibles</flux:text>
            </flux:card>
            <flux:card size="sm" class="text-center space-y-1">
                <flux:heading size="xl" class="!text-2xl">100%</flux:heading>
                <flux:text variant="subtle" class="text-xs">reservas online</flux:text>
            </flux:card>
        </section>
    @endif

    <flux:card class="mt-8 p-6 space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading>Empresas</flux:heading>
            <flux:text variant="subtle" class="text-xs">{{ $stats['empresas'] }} {{ Str::plural('empresa', $stats['empresas']) }}</flux:text>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            @forelse($empresas as $empresa)
                <a href="{{ route('publico.empresa.show', $empresa) }}" class="group">
                    <flux:card size="sm" class="flex items-start gap-4 transition hover:shadow-md group-hover:border-zinc-400 dark:group-hover:border-zinc-500">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-sm font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                            {{ strtoupper(substr($empresa->nombre, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1 space-y-1">
                            <flux:heading class="group-hover:text-zinc-600 dark:group-hover:text-zinc-300">{{ $empresa->nombre }}</flux:heading>
                            @if($empresa->descripcion)
                                <flux:text variant="subtle" class="text-sm">{{ Str::limit($empresa->descripcion, 120) }}</flux:text>
                            @endif
                            <div class="flex flex-wrap items-center gap-2">
                                @if($empresa->servicios_count > 0)
                                    <flux:text variant="subtle" class="text-xs">{{ $empresa->servicios_count }} {{ Str::plural('servicio', $empresa->servicios_count) }}</flux:text>
                                    <flux:text variant="subtle" class="text-xs text-zinc-300 dark:text-zinc-600">&middot;</flux:text>
                                @endif
                                <flux:text variant="subtle" class="text-xs">Reservar &rarr;</flux:text>
                            </div>
                        </div>
                    </flux:card>
                </a>
            @empty
                <div class="col-span-full py-8 text-center">
                    <flux:text variant="subtle" class="text-lg">No hay empresas disponibles por el momento.</flux:text>
                    <flux:text variant="subtle" class="mt-1">Pronto nos estaremos sumando nuevas empresas.</flux:text>
                </div>
            @endforelse
        </div>
    </flux:card>

    <flux:card class="mt-8 p-8 space-y-8">
        <flux:heading size="lg" class="text-center">Cómo funciona</flux:heading>
        <div class="grid gap-6 md:grid-cols-3">
            <flux:card size="sm" class="text-center space-y-2">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-sm font-bold dark:bg-zinc-800">1</div>
                <flux:heading>Elegí una empresa</flux:heading>
                <flux:text variant="subtle" class="text-sm">Navegá por las empresas disponibles y seleccioná la que necesitás.</flux:text>
            </flux:card>
            <flux:card size="sm" class="text-center space-y-2">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-sm font-bold dark:bg-zinc-800">2</div>
                <flux:heading>Elegí servicio y horario</flux:heading>
                <flux:text variant="subtle" class="text-sm">Seleccioná el servicio que querés y el horario disponible que mejor te quede.</flux:text>
            </flux:card>
            <flux:card size="sm" class="text-center space-y-2">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-sm font-bold dark:bg-zinc-800">3</div>
                <flux:heading>Confirmá tu turno</flux:heading>
                <flux:text variant="subtle" class="text-sm">Completá tus datos y recibí la confirmación al instante.</flux:text>
            </flux:card>
        </div>
    </flux:card>

    @guest
        <flux:card class="mt-8 border-zinc-700 bg-zinc-900 p-8 text-center space-y-6 dark:bg-zinc-800">
            <div class="space-y-2">
                <flux:heading class="text-white">¿Tenés un negocio?</flux:heading>
                <flux:text class="text-zinc-300">Sumate a Turnero y ofrecé turnos online a tus clientes de forma simple y sin costo.</flux:text>
            </div>
            <flux:button href="{{ route('register') }}" variant="primary">
                Crear cuenta gratuita
            </flux:button>
        </flux:card>
    @endguest
@endsection
