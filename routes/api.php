<?php

use App\Http\Controllers\Api\Admin\SpotDepositController;
use App\Http\Controllers\Api\Admin\SpotWithdrawController;
use App\Http\Controllers\Internal\AdminFutureChartController;
use App\Http\Controllers\Internal\MarketListController;
use App\Http\Controllers\Internal\TradingChartController;
use App\Http\Controllers\Internal\TradingChartSummaryController;
use App\Http\Controllers\Trade\TradingSessionController;
use App\Http\Middleware\EnsureClientFullyVerified;
use Illuminate\Support\Facades\Route;

Route::prefix('internal/chart')
    ->name('internal.chart.')
    ->middleware([
        // 'auth:sanctum',   // swap for your internal auth middleware
        // 'throttle:60,1',  // 60 requests per minute per IP
    ])
    ->group(function () {

        // GET /api/internal/chart/candles
        Route::get('candles', [TradingChartController::class, 'getCandles'])
            ->name('candles');

        // GET /api/internal/chart/summary
        Route::get('summary', [TradingChartSummaryController::class, 'show'])
            ->name('summary');
        // GET /api/internal/chart/market-list
        Route::get('market-list', [MarketListController::class, 'index'])
            ->name('market-list');

        // POST /api/internal/chart/future-direction
        Route::post('future-direction', [TradingChartController::class, 'updateFutureDirection'])
            ->name('future-direction')
            ->middleware('throttle:10,1'); // stricter limit on write operations

        // POST /api/internal/chart/rewrite-range
        Route::post('rewrite-range', [TradingChartController::class, 'rewriteRange'])
            ->name('rewrite-range')
            ->middleware('throttle:10,1');
    });

Route::prefix('trade')->group(function () {

    Route::get('session/current', [TradingSessionController::class, 'current']);
    Route::get('fee-config', [TradingSessionController::class, 'feeConfig']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('session/{id}/result', [TradingSessionController::class, 'result']);
        Route::get('latest', [TradingSessionController::class, 'latest']);
        Route::get('session/{session_id}/trades', [TradingSessionController::class, 'getTradeBySession']);

        Route::middleware([EnsureClientFullyVerified::class])->group(function () {
            Route::post('buy', [TradingSessionController::class, 'buy']);
            Route::post('sell', [TradingSessionController::class, 'sell']);
        });
    });
});

Route::prefix('admin/spot')->middleware(['auth:sanctum'])->group(function () {
    Route::post('deposit/confirm', [SpotDepositController::class, 'confirm'])
        ->name('admin.spot.deposit.confirm');

    Route::post('withdraw/process', [SpotWithdrawController::class, 'process'])
        ->name('admin.spot.withdraw.process');
});

Route::prefix('admin/future-chart')
    ->name('admin.future-chart.')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('candles', [AdminFutureChartController::class, 'candles'])
            ->name('candles');
    });
