<?php

use Illuminate\Support\Facades\Route;
use Milton\Vaultix\Http\Controllers\VaultixController;

Route::middleware(['web', 'auth'])->prefix('vaultix')->group(function () {
    Route::get('/', [VaultixController::class, 'index'])->name('vaultix.index');
    
    // Destinations
    Route::get('/destinations/create', [VaultixController::class, 'createDestination'])->name('vaultix.destinations.create');
    Route::post('/destinations', [VaultixController::class, 'storeDestination'])->name('vaultix.destinations.store');
    Route::get('/destinations/{destination}/edit', [VaultixController::class, 'editDestination'])->name('vaultix.destinations.edit');
    Route::put('/destinations/{destination}', [VaultixController::class, 'updateDestination'])->name('vaultix.destinations.update');
    Route::delete('/destinations/{destination}', [VaultixController::class, 'destroyDestination'])->name('vaultix.destinations.destroy');
    Route::post('/destinations/{destination}/test', [VaultixController::class, 'testConnection'])->name('vaultix.destinations.test');
    
    // Jobs
    Route::post('/run/{job}', [VaultixController::class, 'runNow'])->name('vaultix.run');

    // Google OAuth Helpers
    Route::get('/auth/google/redirect', [VaultixController::class, 'redirectToGoogle'])->name('vaultix.auth.google.redirect');
    Route::get('/auth/google/callback', [VaultixController::class, 'handleGoogleCallback'])->name('vaultix.auth.google.callback');
});
