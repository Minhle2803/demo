<?php

namespace App\Http\Middleware;

use App\Support\ErrorCodes;
use Closure;
use Illuminate\Http\Request;

class EnsureClientFullyVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('client')->user();
        $user2 = $request->getUser();

       
        if (! $user2) {
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'code' => ErrorCodes::AUTH_UNAUTHORIZED,
                'message' => __('errors.AUTH_UNAUTHORIZED'),
            ], 401);
        }
        $kycComplete = ! empty($user->kyc_front_url) && ! empty($user->kyc_back_url);

        if (! $user->is_verified || ! $kycComplete) {
            return response()->json([
                'success' => false,
                'status_code' => 403,
                'message' => __('errors.USER_NOT_FULLY_VERIFIED'),
                'code' => ErrorCodes::USER_NOT_FULLY_VERIFIED,
            ], 403);
        }

        return $next($request);
    }
}
