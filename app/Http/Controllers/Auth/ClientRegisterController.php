<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ClientRegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\ClientUser;
use App\Services\OtpService;
use App\Support\ErrorCodes;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ClientRegisterController extends Controller
{
    public function __construct(private readonly OtpService $otpService) {}

    /**
     * POST /client/register
     *
     * 1. Validate → 2. Create user → 3. Send email verification →
     * 4. Send phone OTP → 5. Return 201
     */
    public function __invoke(ClientRegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = ClientUser::create([
            'nickname'                   => $validated['nickname'],
            'email'                      => $validated['email'],
            'phone_number'               => $validated['phone_number'],
            'password'                   => $validated['password'], // cast 'hashed' handles bcrypt
            'referral_code'              => $validated['referral_code'] ?? null,
            'is_verified'                => false,
            'balance'                    => 0,
            'trading_balance'            => 0,
            // Email verification token for custom flow
            'email_verification_token'   => Str::random(64),
        ]);

        // Fire Laravel's Registered event → triggers MustVerifyEmail if needed
        event(new Registered($user));

        // Optionally send a custom verification email here:
        // Mail::to($user->email)->send(new ClientEmailVerificationMail($user));

        // Send phone OTP (mocked by default)
        $this->otpService->sendOtp($user);

        return ApiResponse::success(
            data: ['user_id' => $user->user_id],
            code: ErrorCodes::REGISTER_SUCCESS,
            statusCode: 201,
        );
    }
}
