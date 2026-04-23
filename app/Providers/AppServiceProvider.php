<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\SmsProviderInterface;
use App\Services\Sms\MockSmsProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

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
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
