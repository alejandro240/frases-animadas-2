<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

// Ruta principal redirige al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth')->name('home');

Route::middleware(['auth'])->group(function () {
    // Dashboard / Lista de frases del usuario
    Route::get('/dashboard', [App\Http\Controllers\FraseController::class, 'index'])->name('dashboard');

    // Alias para mantener compatibilidad con frases.index
    Route::get('/frases', function () {
        return redirect()->route('dashboard');
    })->name('frases.index');

    // Página para crear nueva frase
    Route::get('/frases/crear', [App\Http\Controllers\FraseController::class, 'create'])->name('frases.create');

    // Almacenar nueva frase
    Route::post('/frases', [App\Http\Controllers\FraseController::class, 'store'])->name('frases.store');

    // Ver animación de una frase
    Route::get('/frases/{frase}', [App\Http\Controllers\FraseController::class, 'show'])->name('frases.show');

    // Eliminar frase
    Route::delete('/frases/{frase}', [App\Http\Controllers\FraseController::class, 'destroy'])->name('frases.destroy');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
