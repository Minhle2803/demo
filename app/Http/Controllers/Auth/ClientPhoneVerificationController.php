<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\ClientUser;
use App\Services\OtpService;
use App\Support\ErrorCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientPhoneVerificationController extends Controller
{
    public function __construct(private readonly OtpService $otpService) {}

    /**
     * POST /client/phone/send-otp
     *
     * Generates and sends OTP to the authenticated client user's phone.
     */
    public function send(Request $request): JsonResponse
    {
        /** @var ClientUser $user */
        $user = auth()->guard('client')->user();

        if ($this->otpService->isRateLimited($user->phone_number)) {
            return ApiResponse::error(
                code: ErrorCodes::AUTH_OTP_TOO_MANY_REQUESTS,
                message: 'Please wait '.$this->otpService->availableIn($user->phone_number).' seconds before retrying.',
                statusCode: 429,
            );
        }

        $sent = $this->otpService->sendOtp($user);

        if (! $sent) {
            return ApiResponse::error(
                code: ErrorCodes::SYSTEM_INTERNAL_ERROR,
                statusCode: 500,
            );
        }

        return ApiResponse::success(
            data: null,
            code: ErrorCodes::PHONE_OTP_SENT,
        );
    }

    /**
     * POST /client/phone/verify-otp
     *
     * Validates the submitted OTP. If email is also verified, marks account fully verified.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'digits:6'],
        ]);

        /** @var ClientUser $user */
        $user = auth()->guard('client')->user();

        $result = $this->otpService->verifyOtp($user, $request->input('otp'));

        return match ($result) {
            'expired' => ApiResponse::error(
                code: ErrorCodes::AUTH_OTP_EXPIRED,
                statusCode: 410,
            ),
            'invalid' => ApiResponse::error(
                code: ErrorCodes::AUTH_INVALID_OTP,
                statusCode: 400,
            ),
            'valid' => $this->handleVerified($user),
        };
    }

    private function handleVerified(ClientUser $user): JsonResponse
    {
        // If email is already verified too → fully verified
        if ($user->hasVerifiedEmail()) {
            $user->markFullyVerified();
        }

        return ApiResponse::success(
            data: ['is_verified' => $user->is_verified],
            code: ErrorCodes::PHONE_VERIFIED_SUCCESS,
        );
    }
}
