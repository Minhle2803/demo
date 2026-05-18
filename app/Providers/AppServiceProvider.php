<?php

namespace App\Providers;

use App\Contracts\SmsProviderInterface;
use App\Services\Sms\MockSmsProvider;
use App\View\Composers\LogoComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Swap MockSmsProvider for a real one (Twilio, Vonage, eSMS) in production.
        $this->app->bind(SmsProviderInterface::class, MockSmsProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        View::composer('*', LogoComposer::class);
    }
}
