<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Support\ErrorCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientLogoutController extends Controller
{
    /**
     * POST /client/logout
     *
     * Logs out only the client guard session. Admin session is unaffected.
     */
    public function __invoke(Request $request): JsonResponse
    {
        Auth::guard('client')->logout();

        // Invalidate & regenerate session token for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return ApiResponse::success(
            data: null,
            code: ErrorCodes::LOGOUT_SUCCESS,
        );
    }
}
