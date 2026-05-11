<?php

namespace App\Services\Referral;

class ReferralRegistrationService
{
    public function __construct(
        protected InviteCodeService $codeService,
    ) {}

    /**
     * Process the referral code during registration and return client fields to set.
     *
     * Returns an associative array of ClientUser attributes to merge during creation.
     */
    public function processReferralCode(?string $referralCode): array
    {
        if ($referralCode === null || $referralCode === '') {
            return [
                'referral_code' => null,
                'invite_code' => null,
                'invited_by_admin_id' => null,
                'invited_by_client_id' => null,
            ];
        }

        $sanitized = $this->sanitize($referralCode);
        if ($sanitized === null) {
            return [
                'referral_code' => $referralCode,
                'invite_code' => null,
                'invited_by_admin_id' => null,
                'invited_by_client_id' => null,
            ];
        }

        $codeType = $this->codeService->codeType($sanitized);

        if ($codeType === null) {
            return [
                'referral_code' => $referralCode,
                'invite_code' => null,
                'invited_by_admin_id' => null,
                'invited_by_client_id' => null,
            ];
        }

        if ($codeType === 'agent') {
            return $this->processAgentCode($sanitized);
        }

        return $this->processAdminCode($sanitized);
    }

    /**
     * Sanitize user input: uppercase, strip non-alphanumeric chars.
     * Returns the cleaned code if it matches a valid pattern, or null.
     */
    private function sanitize(string $code): ?string
    {
        $code = strtoupper(trim($code));
        $stripped = preg_replace('/[^A-Z0-9]/', '', $code);

        if (strlen($stripped) === 5 && preg_match('/^[A-Z][0-9]{4}$/', $stripped)) {
            return $stripped;
        }

        if (strlen($stripped) === 10 && preg_match('/^[A-Z][0-9]{4}[A-Z][0-9]{4}$/', $stripped)) {
            return substr($stripped, 0, 5).'-'.substr($stripped, 5, 5);
        }

        return null;
    }

    protected function processAdminCode(string $code): array
    {
        $admin = $this->codeService->findAdminByCode($code);

        if (! $admin) {
            return [
                'referral_code' => $code,
                'invite_code' => null,
                'invited_by_admin_id' => null,
                'invited_by_client_id' => null,
            ];
        }

        $ownCode = $this->codeService->generateClientCode($code);

        return [
            'referral_code' => $code,
            'invite_code' => $ownCode,
            'invited_by_admin_id' => $admin->id,
            'invited_by_client_id' => null,
        ];
    }

    protected function processAgentCode(string $code): array
    {
        $agent = $this->codeService->findClientByCode($code);

        if (! $agent) {
            return [
                'referral_code' => $code,
                'invite_code' => null,
                'invited_by_admin_id' => null,
                'invited_by_client_id' => null,
            ];
        }

        return [
            'referral_code' => $code,
            'invite_code' => null,
            'invited_by_admin_id' => $agent->invited_by_admin_id,
            'invited_by_client_id' => $agent->id,
        ];
    }
}
