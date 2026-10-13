<?php

use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Email links opened directly in a browser have no SPA Origin header: start the web session.
// The API path still returns the shared JSON response format.
Route::get('/api/email/verify/{id}/{hash}', [UserController::class, 'verifyEmail'])
    ->middleware(['auth:sanctum', 'signed', 'throttle:6,1'])->name('verification.verify');

Route::prefix('artisan')->middleware('guest:web')->group(function (): void {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:login');
});

Route::prefix('artisan')->middleware('auth:web')->group(function (): void {
    Route::view('/account', 'auth.dashboard')->name('runner.dashboard');
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
});
