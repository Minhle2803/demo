<?php

namespace App\Services\Sms;

use App\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Log;

/**
 * Mock SMS provider — logs the OTP to the Laravel log instead of sending a real SMS.
 * Swap this for a real provider (Twilio, Vonage, eSMS, etc.) by binding
 * SmsProviderInterface in AppServiceProvider without changing OtpService.
 */
class MockSmsProvider implements SmsProviderInterface
{
    public function send(string $phone, string $message): bool
    {
        Log::channel('single')->info('[MockSMS] OTP sent', [
            'phone' => $phone,
            'message' => $message,
        ]);

        return true;
    }
}
