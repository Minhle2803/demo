<?php

use App\Http\Controllers\Auth\ClientLoginController;
use App\Http\Controllers\Auth\ClientRegisterController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\TraddingController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/tradding', [TraddingController::class, 'index'])->name('tradding');
Route::get('/spot-trading', [TraddingController::class, 'spot'])->name('spot.trading');

Route::get('/demo', [DemoController::class, 'index'])->name('demo');

Route::middleware('guest')->group(function () {

    Route::get('/signin', [ClientLoginController::class, 'index'])->name('signin');
    Route::get('/signup', [ClientRegisterController::class, 'index'])->name('signup');
});

Route::view('/', 'pages.landing4')->name('landing2');

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'vi'], true)) {
        session(['locale' => $locale]);
        App::setLocale($locale);
    }

    return redirect()->back();
})->name('lang.switch');
