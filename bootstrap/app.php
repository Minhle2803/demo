<?php

use App\Console\Commands\SeedTradingChartCandles;
use App\Console\Commands\TradingChartWorker;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SetLocale;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/client_auth.php'));

            Route::middleware('web')
                ->group(base_path('routes/client_profile.php'));

            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/spot_trading_api.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/spot_trading_admin_api.php'));
        },
    )
    ->withCommands([
        SeedTradingChartCandles::class,
        TradingChartWorker::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'guest' => RedirectIfAuthenticated::class,
            'auth' => Authenticate::class,
        ]);

        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->web(append: [
            SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            // API request
            if ($request->is('api/*') || $request->expectsJson()) {

                return response()->json([
                    'success' => false,
                    'status_code' => 401,
                    'code' => 'AUTH_UNAUTHORIZED',
                    'message' => __('errors.AUTH_UNAUTHORIZED'),
                ], 401);
            }

            if ($request->is('admin/*')) {
                return redirect()->route('admin.login');
            }

            // Web request
            return redirect()->route('signin');
        });
    })->create();
