<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminCryptoAssetController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDepositController;
use App\Http\Controllers\Admin\AdminSessionController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminWithdrawController;
use Illuminate\Support\Facades\Route;

Route::name('admin.')->group(function () {
    Route::get('/admin/signin', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/admin/signin', [AdminAuthController::class, 'login'])->name('login');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');

        Route::get('/deposits', [AdminDepositController::class, 'index'])->name('deposits.index');
        Route::post('/deposits/{id}/approve', [AdminDepositController::class, 'approve'])->name('deposits.approve');
        Route::post('/deposits/{id}/reject', [AdminDepositController::class, 'reject'])->name('deposits.reject');

        Route::get('/withdraws', [AdminWithdrawController::class, 'index'])->name('withdraws.index');
        Route::post('/withdraws/{id}/approve', [AdminWithdrawController::class, 'approve'])->name('withdraws.approve');
        Route::post('/withdraws/{id}/reject', [AdminWithdrawController::class, 'reject'])->name('withdraws.reject');

        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/bank', [AdminSettingController::class, 'updateBank'])->name('settings.bank');
        Route::post('/settings/logo', [AdminSettingController::class, 'updateLogo'])->name('settings.logo');

        Route::get('/settings/crypto-assets', [AdminCryptoAssetController::class, 'index'])->name('crypto-assets.index');
        Route::post('/settings/crypto-assets', [AdminCryptoAssetController::class, 'store'])->name('crypto-assets.store');
        Route::put('/settings/crypto-assets/{id}', [AdminCryptoAssetController::class, 'update'])->name('crypto-assets.update');
        Route::delete('/settings/crypto-assets/{id}', [AdminCryptoAssetController::class, 'destroy'])->name('crypto-assets.destroy');

        Route::get('/sessions', [AdminSessionController::class, 'index'])->name('sessions.index');
        Route::get('/sessions/{id}', [AdminSessionController::class, 'show'])->name('sessions.show');
    });
});
