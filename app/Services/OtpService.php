<?php

namespace App\Services;

use App\Contracts\SmsProviderInterface;
use App\Models\ClientUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Handles all OTP generation, sending, and verification for client phone auth.
 *
 * Injected via constructor DI — never resolved with app() inside controllers.
 */
class OtpService
{
    // OTP digits length
    private const OTP_LENGTH = 6;

    // OTP expiry in minutes
    private const OTP_TTL_MINUTES = 5;

    // Max OTP resend attempts per time window
    private const MAX_RESEND_ATTEMPTS = 3;

    // Time window for rate limit in seconds
    private const RATE_LIMIT_WINDOW = 60;

    public function __construct(private readonly SmsProviderInterface $smsProvider) {}

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Generate, store, and dispatch OTP for a client user.
     *
     * @throws \RuntimeException if rate limit is exceeded
     */
    public function sendOtp(ClientUser $user): bool
    {
        $key = $this->rateLimitKey($user->phone_number);

        if (RateLimiter::tooManyAttempts($key, self::MAX_RESEND_ATTEMPTS)) {
            return false; // caller should return AUTH_OTP_TOO_MANY_REQUESTS
        }

        RateLimiter::hit($key, self::RATE_LIMIT_WINDOW);

        $otp = $this->generateOtp();

        $user->forceFill([
            'phone_otp_code' => Hash::make($otp),
            'phone_otp_expired_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
        ])->save();

        $message = "Your verification code is: {$otp}. Valid for ".self::OTP_TTL_MINUTES.' minutes.';

        return $this->smsProvider->send($user->phone_number, $message);
    }

    /**
     * Validate an OTP submitted by the user.
     *
     * Returns one of: 'valid' | 'expired' | 'invalid'
     */
    public function verifyOtp(ClientUser $user, string $submittedOtp): string
    {
        if ($user->phone_otp_code === null) {
            return 'invalid';
        }

        if (now()->isAfter($user->phone_otp_expired_at)) {
            return 'expired';
        }

        if (! Hash::check($submittedOtp, $user->phone_otp_code)) {
            return 'invalid';
        }

        // Consume the OTP so it cannot be reused
        $user->forceFill([
            'phone_otp_code' => null,
            'phone_otp_expired_at' => null,
        ])->save();

        // Clear the rate limiter on success
        RateLimiter::clear($this->rateLimitKey($user->phone_number));

        return 'valid';
    }

    /**
     * Check if a phone number is currently rate-limited.
     */
    public function isRateLimited(string $phone): bool
    {
        return RateLimiter::tooManyAttempts(
            $this->rateLimitKey($phone),
            self::MAX_RESEND_ATTEMPTS
        );
    }

    /**
     * Seconds remaining before the rate limit resets.
     */
    public function availableIn(string $phone): int
    {
        return RateLimiter::availableIn($this->rateLimitKey($phone));
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function generateOtp(): string
    {
        // Cryptographically random numeric OTP
        return str_pad((string) random_int(0, (int) str_repeat('9', self::OTP_LENGTH)), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    private function rateLimitKey(string $phone): string
    {
        return 'otp_send:'.$phone;
    }
}
