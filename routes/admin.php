<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminCryptoAssetController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDepositController;
use App\Http\Controllers\Admin\AdminIpWhitelistController;
use App\Http\Controllers\Admin\AdminReferralController;
use App\Http\Controllers\Admin\AdminSessionController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminWithdrawController;
use App\Http\Controllers\Admin\ChangePasswordController;
use Illuminate\Support\Facades\Route;

Route::name('admin.')->group(function () {
    Route::get('/admin/signin', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/admin/signin', [AdminAuthController::class, 'login'])->name('login');
    Route::get('/admin/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/kyc', [AdminUserController::class, 'kyc'])->name('users.kyc');
        Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{id}/approve-kyc', [AdminUserController::class, 'approveKyc'])->name('users.approve-kyc');

        Route::get('/deposits', [AdminDepositController::class, 'index'])->name('deposits.index');
        Route::post('/deposits/{id}/approve', [AdminDepositController::class, 'approve'])->name('deposits.approve');
        Route::post('/deposits/{id}/reject', [AdminDepositController::class, 'reject'])->name('deposits.reject');

        Route::get('/withdraws', [AdminWithdrawController::class, 'index'])->name('withdraws.index');
        Route::post('/withdraws/{id}/approve', [AdminWithdrawController::class, 'approve'])->name('withdraws.approve');
        Route::post('/withdraws/{id}/reject', [AdminWithdrawController::class, 'reject'])->name('withdraws.reject');

        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/bank', [AdminSettingController::class, 'updateBank'])->name('settings.bank');
        Route::post('/settings/logo', [AdminSettingController::class, 'updateLogo'])->name('settings.logo');
        Route::post('/settings/fee', [AdminSettingController::class, 'updateFee'])->name('settings.fee');
        Route::post('/settings/min-deposit', [AdminSettingController::class, 'updateMinDeposit'])->name('settings.min-deposit');
        Route::post('/settings/ip-whitelist', [AdminSettingController::class, 'updateIpWhitelist'])->name('settings.ip-whitelist');

        Route::get('/settings/crypto-assets', [AdminCryptoAssetController::class, 'index'])->name('crypto-assets.index');
        Route::post('/settings/crypto-assets', [AdminCryptoAssetController::class, 'store'])->name('crypto-assets.store');
        Route::put('/settings/crypto-assets/{id}', [AdminCryptoAssetController::class, 'update'])->name('crypto-assets.update');
        Route::delete('/settings/crypto-assets/{id}', [AdminCryptoAssetController::class, 'destroy'])->name('crypto-assets.destroy');

        Route::get('/referrals', [AdminReferralController::class, 'index'])->name('referrals.index');
        Route::get('/referrals/{clientUserId}', [AdminReferralController::class, 'show'])->name('referrals.show');

        Route::get('/change-password', [ChangePasswordController::class, 'showForm'])->name('change-password');
        Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('change-password.update');

        Route::get('/sessions', [AdminSessionController::class, 'index'])->name('sessions.index');
        Route::get('/sessions/{id}', [AdminSessionController::class, 'show'])->name('sessions.show');

        Route::get('/ip-whitelist', [AdminIpWhitelistController::class, 'index'])->name('ip-whitelist.index');
        Route::post('/ip-whitelist', [AdminIpWhitelistController::class, 'store'])->name('ip-whitelist.store');
        Route::put('/ip-whitelist/{id}', [AdminIpWhitelistController::class, 'update'])->name('ip-whitelist.update');
        Route::delete('/ip-whitelist/{id}', [AdminIpWhitelistController::class, 'destroy'])->name('ip-whitelist.destroy');
    });
});
