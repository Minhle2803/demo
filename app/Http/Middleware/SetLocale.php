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
            ?? $this->parseAcceptLanguage($request->header('Accept-Language'))
            ?? config('app.locale', 'en');

        if (! in_array($locale, $this->supportedLocales, true)) {
            $locale = config('app.locale', 'en');
        }

        if (Session::get('locale') !== $locale) {
            Session::put('locale', $locale);
        }

        App::setLocale($locale);

        return $next($request);
    }

    protected function parseAcceptLanguage(?string $header): ?string
    {
        if (! $header) {
            return null;
        }

        foreach (explode(',', $header) as $part) {
            $segments = explode(';q=', $part);
            $code = strtolower(trim($segments[0]));
            $lang = substr($code, 0, 2);

            if (in_array($lang, $this->supportedLocales, true)) {
                return $lang;
            }
        }

        return null;
    }
}
