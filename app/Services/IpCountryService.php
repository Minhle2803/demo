<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class IpCountryService
{
    private const API_URL = 'http://ip-api.com/json/%s?fields=countryCode,country';

    public function getCountryCode(string $ip): ?string
    {
        if ($this->isPrivateIp($ip)) {
            return null;
        }

        return Cache::remember("ip_country:{$ip}", now()->addDay(), function () use ($ip) {
            return $this->fetchCountryCode($ip);
        });
    }

    private function fetchCountryCode(string $ip): ?string
    {
        try {
            $response = Http::timeout(5)->get(sprintf(self::API_URL, $ip));

            if ($response->successful() && $response->json('status') !== 'fail') {
                return $response->json('countryCode');
            }
        } catch (\Exception $e) {
            report($e);
        }

        return null;
    }

    private function isPrivateIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
