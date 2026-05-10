<?php

namespace App\Services\Referral;

use App\Models\ClientUser;
use App\Models\User;

class InviteCodeService
{
    private const ADMIN_PATTERN = '/^[A-Z][0-9]{4}$/';

    private const AGENT_PATTERN = '/^[A-Z][0-9]{4}-[A-Z][0-9]{4}$/';

    private const MAX_RETRIES = 10;

    /**
     * Generate a unique admin invite code (Axxxx format).
     */
    public function generateAdminCode(): string
    {
        $attempts = 0;
        do {
            $code = $this->generateCodeSegment();
            $attempts++;
        } while (User::where('invite_code', $code)->exists() && $attempts < self::MAX_RETRIES);

        if ($attempts >= self::MAX_RETRIES) {
            throw new \RuntimeException('Failed to generate unique admin invite code.');
        }

        return $code;
    }

    /**
     * Generate a client invite code by appending a random segment to the admin prefix.
     */
    public function generateClientCode(string $adminCode): string
    {
        $attempts = 0;
        do {
            $code = $adminCode.'-'.$this->generateCodeSegment();
            $attempts++;
        } while (ClientUser::where('invite_code', $code)->exists() && $attempts < self::MAX_RETRIES);

        if ($attempts >= self::MAX_RETRIES) {
            throw new \RuntimeException('Failed to generate unique client invite code.');
        }

        return $code;
    }

    /**
     * Determine the type of invite code.
     */
    public function codeType(?string $code): ?string
    {
        if ($code === null || $code === '') {
            return null;
        }

        if (preg_match(self::AGENT_PATTERN, $code)) {
            return 'agent';
        }

        if (preg_match(self::ADMIN_PATTERN, $code)) {
            return 'admin';
        }

        return null;
    }

    /**
     * Extract the admin prefix from any valid code.
     * - "K4821" -> "K4821"
     * - "K4821-P1938" -> "K4821"
     */
    public function extractAdminPrefix(string $code): string
    {
        return explode('-', $code)[0];
    }

    /**
     * Find the admin user who owns the given code.
     */
    public function findAdminByCode(string $code): ?User
    {
        $adminCode = $this->extractAdminPrefix($code);

        return User::where('invite_code', $adminCode)->first();
    }

    /**
     * Find the client user who owns the given agent code.
     */
    public function findClientByCode(string $code): ?ClientUser
    {
        return ClientUser::where('invite_code', $code)->first();
    }

    /**
     * Validate a referral code format.
     */
    public function isValidCode(?string $code): bool
    {
        return $this->codeType($code) !== null;
    }

    /**
     * Generate a single code segment: one uppercase letter + 4 random digits.
     */
    private function generateCodeSegment(): string
    {
        $letter = chr(rand(65, 90)); // A-Z
        $digits = str_pad((string) rand(0, 9999), 4, '0', STR_PAD_LEFT);

        return $letter.$digits;
    }
}
