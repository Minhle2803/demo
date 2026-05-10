<?php

namespace App\Services\Referral;

use App\Models\ClientUser;

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

        $codeType = $this->codeService->codeType($referralCode);

        if ($codeType === null) {
            return [
                'referral_code' => $referralCode,
                'invite_code' => null,
                'invited_by_admin_id' => null,
                'invited_by_client_id' => null,
            ];
        }

        if ($codeType === 'agent') {
            return $this->processAgentCode($referralCode);
        }

        return $this->processAdminCode($referralCode);
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

        $adminPrefix = $this->codeService->extractAdminPrefix($code);
        $ownCode = $this->codeService->generateClientCode($adminPrefix);

        return [
            'referral_code' => $code,
            'invite_code' => $ownCode,
            'invited_by_admin_id' => $agent->invited_by_admin_id,
            'invited_by_client_id' => $agent->id,
        ];
    }
}
