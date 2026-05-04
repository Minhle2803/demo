<?php

use App\Http\Controllers\Api\Admin\Spot\AdminSpotOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/spot')->middleware(['auth:sanctum'])->group(function () {

    Route::get('orders/open', [AdminSpotOrderController::class, 'openOrders']);

    Route::post('orders/{id}/manual-match', [AdminSpotOrderController::class, 'manualMatch']);
});
