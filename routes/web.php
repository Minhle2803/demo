<?php

use App\Http\Controllers\TraddingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ClientLoginController;
use App\Http\Controllers\Auth\ClientRegisterController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/tradding', [TraddingController::class, 'index'])->name('tradding');
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::get('/demo', [DemoController::class, 'index'])->name('demo');
Route::get('/trading2', fn() => view('pages.session'))->name('trading.session');
Route::get('/signin', [ClientLoginController::class, 'index'])->name('signin');
Route::get('/signup', [ClientRegisterController::class, 'index'])->name('signup');
