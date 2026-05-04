<?php

use App\Http\Controllers\Api\Spot\SpotOrderController;
use App\Http\Controllers\Api\Spot\SpotTradeController;
use App\Http\Controllers\Api\Spot\SpotWalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('spot')->middleware(['auth:sanctum'])->group(function () {

    Route::post('orders/buy', [SpotOrderController::class, 'buy']);
    Route::post('orders/sell', [SpotOrderController::class, 'sell']);
    Route::post('orders/{id}/cancel', [SpotOrderController::class, 'cancel']);
    Route::get('orders', [SpotOrderController::class, 'myOrders']);

    Route::get('trades', [SpotTradeController::class, 'myTrades']);

    Route::get('wallets', [SpotWalletController::class, 'myWallets']);

    Route::get('orderbook', [SpotOrderController::class, 'orderBook']);
});
