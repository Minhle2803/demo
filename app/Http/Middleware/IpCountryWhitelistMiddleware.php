<?php

namespace App\Http\Middleware;

use App\Models\IpCountryWhitelist;
use App\Models\ProjectSetting;
use App\Services\IpCountryService;
use Closure;
use Illuminate\Http\Request;

class IpCountryWhitelistMiddleware
{
    public function __construct(
        protected IpCountryService $ipCountryService,
    ) {}

    public function handle(Request $request, Closure $next)
    {
        if ($request->is('admin', 'admin/*')) {
            return $next($request);
        }

        if (! $this->isWhitelistEnabled()) {
            return $next($request);
        }

        $ip = $request->ip();
        $countryCode = $this->ipCountryService->getCountryCode($ip);

        if ($countryCode === null) {
            return $next($request);
        }

        $isWhitelisted = IpCountryWhitelist::where('country_code', $countryCode)->exists();

        if (! $isWhitelisted) {
            return response()->view('pages.maintain', [], 503);
        }

        return $next($request);
    }

    private function isWhitelistEnabled(): bool
    {
        return ProjectSetting::getValue('ip_whitelist_enabled', '1') === '1';
    }
}
