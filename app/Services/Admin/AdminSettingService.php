<?php

namespace App\Services\Admin;

use App\Models\ProjectSetting;
use Illuminate\Http\UploadedFile;

class AdminSettingService
{
    public function updateBank(array $data): void
    {
        if (isset($data['bank_name'])) {
            ProjectSetting::setValue('deposit_bank_name', $data['bank_name']);
        }
        if (isset($data['bank_account'])) {
            ProjectSetting::setValue('deposit_bank_account', $data['bank_account']);
        }
        if (isset($data['bank_number'])) {
            ProjectSetting::setValue('deposit_bank_number', $data['bank_number']);
        }
    }

    public function updateLogo(UploadedFile $file): string
    {
        $filename = $file->hashName();
        $file->move(public_path('assets/images/logo/upload'), $filename);
        $url = asset('assets/images/logo/upload/'.$filename);

        ProjectSetting::setValue('project_logo', $url);

        return $url;
    }

    public function getBankInfo(): array
    {
        return [
            'bank_name' => ProjectSetting::getValue('deposit_bank_name'),
            'bank_account' => ProjectSetting::getValue('deposit_bank_account'),
            'bank_number' => ProjectSetting::getValue('deposit_bank_number'),
        ];
    }

    public function getLogo(): ?string
    {
        return ProjectSetting::getValue('project_logo', asset('assets/images/logo/tradex_logo.png'));
    }

    public function getFeePercent(): float
    {
        return (float) ProjectSetting::getValue('trading_fee_percent', '5');
    }

    public function getMinDeposit(): float
    {
        return (float) ProjectSetting::getValue('deposit_min_amount', '300000');
    }

    public function updateFeePercent(float $percent): void
    {
        ProjectSetting::setValue('trading_fee_percent', (string) $percent, 'numeric');
    }

    public function updateMinDeposit(float $amount): void
    {
        ProjectSetting::setValue('deposit_min_amount', (string) $amount, 'numeric');
    }

    public function getIpWhitelistEnabled(): bool
    {
        return ProjectSetting::getValue('ip_whitelist_enabled', '1') === '1';
    }

    public function updateIpWhitelistEnabled(bool $enabled): void
    {
        ProjectSetting::setValue('ip_whitelist_enabled', $enabled ? '1' : '0');
    }
}
