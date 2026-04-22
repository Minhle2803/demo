<?php

use App\Http\Controllers\TraddingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/tradding', [TraddingController::class, 'index'])->name('tradding');
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
