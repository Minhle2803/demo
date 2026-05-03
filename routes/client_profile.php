<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client Profile Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /profile and protected by auth:client.
| Named routes use the "client.profile." prefix.
|
*/

Route::prefix('profile')->name('client.profile.')->middleware(['auth:client'])->group(function () {

    Route::get('/', [ProfileController::class, 'show'])->name('show');

    Route::post('/update', [ProfileController::class, 'updateProfile'])->name('update');

    Route::post('/bank', [ProfileController::class, 'updateBankInfo'])->name('bank');

    Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password');

    Route::post('/deposit/qr', [ProfileController::class, 'generateDepositQr'])->name('deposit.qr');

    Route::get('/deposit/history', [ProfileController::class, 'depositHistory'])->name('deposit.history');

    Route::post('/withdraw', [ProfileController::class, 'submitWithdraw'])->name('withdraw');

    Route::get('/withdraw/history', [ProfileController::class, 'withdrawHistory'])->name('withdraw.history');

    Route::post('/kyc', [ProfileController::class, 'submitKyc'])->name('kyc');
});
