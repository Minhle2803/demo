<?php

use App\Http\Controllers\TraddingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/tradding', [TraddingController::class, 'index'])->name('tradding');
