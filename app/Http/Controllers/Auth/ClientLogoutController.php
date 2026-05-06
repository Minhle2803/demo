<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Support\ErrorCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientLogoutController extends Controller
{
    /**
     * GET /client/logout
     *
     * Logs out only the client guard session and revokes all Sanctum tokens.
     * Admin session is unaffected.
     */
    public function __invoke(Request $request): JsonResponse|RedirectResponse
    {
        $request->user()?->tokens()->delete();

        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return ApiResponse::success(
                data: null,
                code: ErrorCodes::LOGOUT_SUCCESS,
            );
        }

        return redirect()->route('landing2');
    }
}
