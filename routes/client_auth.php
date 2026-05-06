<?php

use App\Http\Controllers\Auth\ClientEmailVerificationController;
use App\Http\Controllers\Auth\ClientForgotPasswordController;
use App\Http\Controllers\Auth\ClientLoginController;
use App\Http\Controllers\Auth\ClientLogoutController;
use App\Http\Controllers\Auth\ClientPhoneVerificationController;
use App\Http\Controllers\Auth\ClientRegisterController;
use App\Http\Controllers\Auth\ClientResetPasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client Auth Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /client and named with client.*
| Throttle middleware is applied per security best practices.
| This file is completely isolated from admin routes.
|
*/

Route::prefix('client')->name('client.')->group(function () {

    // -------------------------------------------------------------------------
    // Public auth routes (guest only — no auth middleware)
    // -------------------------------------------------------------------------
    Route::middleware(['throttle:auth'])->group(function () {
        Route::post('/register', ClientRegisterController::class)
            ->name('register');

        Route::post('/login', ClientLoginController::class)
            ->name('login');
    });

    // -------------------------------------------------------------------------
    // Password reset (public — throttled separately)
    // -------------------------------------------------------------------------
    Route::middleware(['throttle:6,1'])->group(function () {
        Route::post('/forgot-password', ClientForgotPasswordController::class)
            ->name('password.forgot');

        Route::post('/reset-password', ClientResetPasswordController::class)
            ->name('password.reset');
    });

    // -------------------------------------------------------------------------
    // Authenticated client routes (requires client guard)
    // -------------------------------------------------------------------------
    Route::middleware(['auth:client'])->group(function () {

        // Logout
        Route::get('/logout', ClientLogoutController::class)
            ->name('logout');

        // Email verification
        Route::post('/email/send-verification', [ClientEmailVerificationController::class, 'send'])
            ->name('email.send-verification');

        // Phone OTP — extra throttle on send
        Route::post('/phone/send-otp', [ClientPhoneVerificationController::class, 'send'])
            ->middleware('throttle:3,1')
            ->name('phone.send-otp');

        Route::post('/phone/verify-otp', [ClientPhoneVerificationController::class, 'verify'])
            ->name('phone.verify-otp');
    });

    // -------------------------------------------------------------------------
    // Email verification link (public — signed URL or token in path)
    // -------------------------------------------------------------------------
    Route::get('/email/verify/{id}/{token}', [ClientEmailVerificationController::class, 'verify'])
        ->name('email.verify');

});
