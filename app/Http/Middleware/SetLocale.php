<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    protected array $supportedLocales = ['en', 'vi'];

    public function handle(Request $request, Closure $next)
    {
        $locale = Session::get('locale')
            ?? $request->query('lang')
            ?? config('app.locale', 'vi');

        if (! in_array($locale, $this->supportedLocales, true)) {
            $locale = config('app.locale', 'en');
        }

        if (Session::get('locale') !== $locale) {
            Session::put('locale', $locale);
        }

        App::setLocale($locale);

        return $next($request);
    }
}
