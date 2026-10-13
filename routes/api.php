<?php

use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/status', [SystemController::class, 'status']);

Route::post('/login', [UserController::class, 'login'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', [UserController::class, 'me']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/email/verify', [UserController::class, 'emailVerificationStatus'])->name('verification.notice');
    Route::post('/email/verification-notification', [UserController::class, 'sendEmailVerification'])
        ->middleware('throttle:email-verification')->name('verification.send');
});
