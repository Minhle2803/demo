<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\ClientUser;
use App\Support\ErrorCodes;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ClientResetPasswordController extends Controller
{
    /**
     * POST /client/reset-password
     *
     * Accepts token, email, password, password_confirmation.
     * Uses the dedicated 'client_users' broker — never touches admin resets.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'token'                 => ['required', 'string'],
            'email'                 => ['required', 'email'],
            'password'              => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
        ]);

        $status = Password::broker('client_users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (ClientUser $user, string $password): void {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return ApiResponse::success(
                data: null,
                code: ErrorCodes::PASSWORD_RESET_SUCCESS,
            );
        }

        // Invalid token or email mismatch
        return ApiResponse::error(
            code: ErrorCodes::AUTH_INVALID_RESET_TOKEN,
            statusCode: 400,
        );
    }
}
