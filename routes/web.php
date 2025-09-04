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

    // Ruta para el formulario de creación de empresas
    Route::get('empresas/crear', \App\Livewire\EmpresaCreate::class)->name('empresas.create');

    // Ruta para mostrar los datos de una empresa específica
    Route::get('empresas/{empresa}', \App\Livewire\EmpresaShow::class)->name('empresas.show');
});

require __DIR__.'/auth.php';
