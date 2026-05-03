<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\ClientUser;
use App\Support\ErrorCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientEmailVerificationController extends Controller
{
    /**
     * POST /client/email/send-verification
     *
     * (Re-)sends the verification email to the authenticated client user.
     */
    public function send(Request $request): JsonResponse
    {
        /** @var ClientUser $user */
        $user = auth()->guard('client')->user();

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(
                data: null,
                code: ErrorCodes::EMAIL_VERIFIED_SUCCESS,
            );
        }

        // Regenerate token and (re-)send email
        $user->forceFill(['email_verification_token' => Str::random(64)])->save();

        // TODO: dispatch ClientEmailVerificationMail — example:
        // Mail::to($user->email)->send(new ClientEmailVerificationMail($user));

        return ApiResponse::success(
            data: null,
            code: ErrorCodes::EMAIL_VERIFICATION_SENT,
        );
    }

    /**
     * GET /client/email/verify/{id}/{hash}
     *
     * Verifies email via signed URL or token.
     * Route: /client/email/verify/{id}/{token}
     */
    public function verify(Request $request, int $id, string $token): JsonResponse
    {
        $user = ClientUser::findOrFail($id);

        if ($user->email_verification_token !== $token) {
            return ApiResponse::error(
                code: ErrorCodes::AUTH_INVALID_RESET_TOKEN,
                statusCode: 400,
            );
        }

        // Consume the token
        $user->forceFill(['email_verification_token' => null])->save();

        // If phone is also verified → fully verified
        if ($user->hasVerifiedPhone()) {
            $user->markFullyVerified();
        }

        return ApiResponse::success(
            data: ['is_verified' => $user->is_verified],
            code: ErrorCodes::EMAIL_VERIFIED_SUCCESS,
        );
    }
}
