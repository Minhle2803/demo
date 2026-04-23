<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Support\ErrorCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ClientForgotPasswordController extends Controller
{
    /**
     * POST /client/forgot-password
     *
     * Sends a password reset link to the client user's email.
     * Uses the dedicated 'client_users' password broker — completely isolated
     * from any admin/web password reset flow.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // 'client_users' broker → reads from config/auth.php passwords.client_users
        $status = Password::broker('client_users')->sendResetLink(
            $request->only('email')
        );

        // Always return success to prevent email enumeration attacks.
        // The status tells us what actually happened, but we don't expose it.
        if ($status === Password::RESET_LINK_SENT) {
            return ApiResponse::success(
                data: null,
                code: ErrorCodes::PASSWORD_RESET_LINK_SENT,
            );
        }

        // Throttled — too many requests
        if ($status === Password::RESET_THROTTLED) {
            return ApiResponse::error(
                code: ErrorCodes::AUTH_OTP_TOO_MANY_REQUESTS,
                statusCode: 429,
            );
        }

        // User not found → return same success shape (security: no enumeration)
        return ApiResponse::success(
            data: null,
            code: ErrorCodes::PASSWORD_RESET_LINK_SENT,
        );
    }
}
