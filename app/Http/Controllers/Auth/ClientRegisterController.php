<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ClientRegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\ClientUser;
use App\Services\OtpService;
use App\Services\Referral\ReferralRegistrationService;
use App\Support\ErrorCodes;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class ClientRegisterController extends Controller
{
    public function index()
    {
        return view('pages.auth.signup');
    }

    public function __construct(
        private readonly OtpService $otpService,
        private readonly ReferralRegistrationService $referralService,
    ) {}

    /**
     * POST /client/register
     *
     * 1. Validate → 2. Process referral → 3. Create user → 4. Send email verification →
     * 5. Send phone OTP → 6. Return 201
     */
    public function __invoke(ClientRegisterRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $referralData = $this->referralService->processReferralCode(
            $validated['referral_code'] ?? null
        );

        $user = ClientUser::create(array_merge([
            'nickname' => $validated['nickname'],
            'phone_number' => $validated['phone_number'],
            'password' => $validated['password'],
            'is_verified' => false,
            'balance' => 0,
            'trading_balance' => 0,
            'email_verification_token' => Str::random(64),
        ], $referralData));

        event(new Registered($user));

        $this->otpService->sendOtp($user);

        if ($request->expectsJson()) {
            return ApiResponse::success(
                data: ['user_id' => $user->user_id],
                code: ErrorCodes::REGISTER_SUCCESS,
                statusCode: 201,
            );
        }

        return redirect()->route('signin');
    }
}
