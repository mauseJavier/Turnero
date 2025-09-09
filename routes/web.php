<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');






    Route::middleware(['role:admin|super'])->group(function () {

        // Ruta para ver los turnos solicitados de la empresa del usuario logueado
        Route::get('empresa/turnos', \App\Livewire\EmpresaTurnos::class)->name('empresa.turnos');

            // Ruta para ver los turnos disponibles por recurso de la empresa del usuario logueado
            Route::get('empresa/turnos-disponibles-por-recurso', \App\Livewire\TurnosDisponiblesPorRecurso::class)
                ->name('empresa.turnos-disponibles-por-recurso');

    });

    Route::middleware(['role:super'])->group(function () {

        // // Ruta para ver los turnos solicitados de la empresa del usuario logueado
        // Route::get('empresa/turnos', \App\Livewire\EmpresaTurnos::class)->name('empresa.turnos');

        // Ruta para el formulario de creación de empresas
        Route::get('empresas/crear', \App\Livewire\EmpresaCreate::class)->name('empresas.create');

        // Ruta para mostrar los datos de una empresa específica
        Route::get('empresas/{empresa}', \App\Livewire\EmpresaShow::class)->name('empresas.show');


    });


});

require __DIR__.'/auth.php';
