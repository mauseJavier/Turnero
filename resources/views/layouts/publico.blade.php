<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>
{{-- <body class="min-h-screen bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100"> --}}
<body class="min-h-screen bg-white dark:bg-zinc-800">


    
    <flux:main class="mx-auto max-w-5xl px-4 py-10">
        <flux:header class="mx-auto flex max-w-5xl items-center justify-between px-4 pt-4">
            <a href="{{ route('home') }}" class="text-sm font-semibold"> {{ $title ?? config('app.name') }} </a>
            <nav class="flex items-center gap-2 text-sm">
    
                @if (Route::has('login'))
                    @auth
                        <flux:button href="{{ url('/dashboard') }}" variant="ghost">Dashboard</flux:button>
                    @else
                        <flux:button href="{{ route('login') }}" variant="ghost">Ingresar</flux:button>
                        @if (Route::has('register'))
                            <flux:button href="{{ route('register') }}" variant="primary">Registrarse</flux:button>
                        @endif
                    @endauth
                @endif
            </nav>
        </flux:header>

        @yield('content')

        <div
            class="fixed z-50"
            style="right: max(1rem, env(safe-area-inset-right)); bottom: max(1rem, env(safe-area-inset-bottom));"
        >
            <flux:button
                onclick="document.documentElement.classList.contains('dark') ? window.Flux.applyAppearance('light') : window.Flux.applyAppearance('dark')"
                class="flex items-center justify-center rounded-full border-4 border-red-600 bg-white p-3 shadow-lg ring-2 ring-red-200/70 transition hover:border-red-700 hover:ring-red-300 dark:border-red-500 dark:bg-zinc-800 dark:ring-red-900/60"
                aria-label="Alternar modo oscuro"
                title="Alternar modo oscuro"
            >
                <svg class="block h-5 w-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
                <svg class="hidden h-5 w-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
            </flux:button>
        </div>
    </flux:main>

</body>
</html> 
