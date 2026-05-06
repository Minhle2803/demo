<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('client')->check()) {
            return redirect()->route('tradding');
        }

        return $next($request);
    }
}
