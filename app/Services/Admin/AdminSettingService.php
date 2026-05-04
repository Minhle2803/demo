<?php

namespace App\Services\Admin;

use App\Models\ProjectSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
        $path = $file->store('public/logo');
        $url = Storage::url($path);
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
        return ProjectSetting::getValue('project_logo', asset('assets/images/logo/logo.png'));
    }
}
