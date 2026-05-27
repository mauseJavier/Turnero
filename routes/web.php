<?php

use App\Http\Controllers\Publico\EmpresaLandingController;
use App\Http\Controllers\Publico\EmpresaIndexController;
use App\Http\Controllers\Publico\ReservaController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', [EmpresaIndexController::class, 'index'])->name('home');

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

        // Ruta para gestión de usuarios, roles y permisos
        Route::get('usuarios', function () {
            return view('usuarios.index');
        })->name('usuarios.management');

    });


});

require __DIR__.'/auth.php';

Route::get('/{empresa:slug}', [EmpresaLandingController::class, 'show'])->name('publico.empresa.show');
Route::get('/{empresa:slug}/reservar', [ReservaController::class, 'create'])->name('publico.reserva.create');
Route::post('/{empresa:slug}/reservar', [ReservaController::class, 'store'])->name('publico.reserva.store');
Route::get('/reserva/{token}/resultado', [ReservaController::class, 'resultado'])->name('publico.reserva.resultado');
