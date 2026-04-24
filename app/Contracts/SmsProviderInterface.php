<?php

namespace App\Contracts;

/**
 * Contract for SMS providers.
 *
 * Bind a concrete implementation in AppServiceProvider (or a dedicated provider):
 *
 *   $this->app->bind(SmsProviderInterface::class, TwilioSmsProvider::class);
 *
 * For now, MockSmsProvider is the default (logs OTP to storage/logs/laravel.log).
 */
interface SmsProviderInterface
{
    /**
     * Send an SMS message to a phone number.
     *
     * @param  string $phone   E.164 or local format phone number
     * @param  string $message Message body
     * @return bool            True on success, false on failure
     */
    public function send(string $phone, string $message): bool;
}
