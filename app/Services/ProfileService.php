<?php

namespace App\Services;

use App\Models\ClientUser;
use App\Models\WithdrawRequest;
use App\Support\ErrorCodes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Zxing\QrReader;

class ProfileService
{
    public function updateProfile(ClientUser $user, array $data): void
    {
        $user->update(['nickname' => $data['nickname']]);
    }

    public function updatePassword(ClientUser $user, string $currentPassword, string $newPassword): bool
    {
        if (! Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->update(['password' => $newPassword]);

        return true;
    }

    public function submitKyc(ClientUser $user, UploadedFile $frontFile, UploadedFile $backFile, array $identityData): array
    {
        if ($user->isKycVerified()) {
            return ['code' => ErrorCodes::KYC_ALREADY_VERIFIED];
        }

        $frontPath = $this->storeKycFile($user, $frontFile, 'front');
        $backPath = $this->storeKycFile($user, $backFile, 'back');

        $user->update([
            'kyc_front_url' => $frontPath,
            'kyc_back_url' => $backPath,
            'full_name' => $identityData['full_name'],
            'date_of_birth' => $identityData['date_of_birth'],
            'cccd_number' => $identityData['cccd_number'],
        ]);

        // Try QR scan on front image for CCCD verification
        $qrResult = $this->scanCccdQr($frontFile);

        if ($qrResult === null) {
            return ['code' => ErrorCodes::KYC_QR_SCAN_FAILED];
        }

        if (! $this->matchKycData($user, $qrResult)) {
            return ['code' => ErrorCodes::KYC_DATA_MISMATCH];
        }

        $user->forceFill(['kyc_verified_at' => now()])->save();

        return ['code' => ErrorCodes::KYC_VERIFIED_SUCCESS];
    }

    private function storeKycFile(ClientUser $user, UploadedFile $file, string $type): string
    {
        $filename = $user->user_id.'_'.$type.'_'.time().'.'.$file->getClientOriginalExtension();

        return Storage::disk('public')->putFileAs('kyc', $file, $filename);
    }

    private function scanCccdQr(UploadedFile $file): ?array
    {
        try {
            $qrReader = new QrReader($file->getRealPath());
            $qrText = $qrReader->text();

            if (empty($qrText)) {
                return null;
            }

            // Vietnamese CCCD QR format: cccd|fullname|dob|gender|...
            $parts = explode('|', $qrText);

            return [
                'cccd' => $parts[0] ?? '',
                'full_name' => $parts[1] ?? '',
                'dob' => $parts[2] ?? '',
            ];
        } catch (\Exception) {
            return null;
        }
    }

    private function matchKycData(ClientUser $user, array $qrData): bool
    {
        $matches = 0;
        $checks = 0;

        if (! empty($user->cccd_number) && ! empty($qrData['cccd'])) {
            $checks++;
            if ($this->normalize($user->cccd_number) === $this->normalize($qrData['cccd'])) {
                $matches++;
            }
        }

        if (! empty($user->full_name) && ! empty($qrData['full_name'])) {
            $checks++;
            if ($this->normalize($user->full_name) === $this->normalize($qrData['full_name'])) {
                $matches++;
            }
        }

        if (! empty($user->date_of_birth) && ! empty($qrData['dob'])) {
            $checks++;
            $userDob = $user->date_of_birth instanceof \DateTime
                ? $user->date_of_birth->format('d/m/Y')
                : $user->date_of_birth;
            if ($this->normalize($userDob) === $this->normalize($qrData['dob'])) {
                $matches++;
            }
        }

        // Require at least 2 fields to match for verification to pass
        return $checks > 0 && $matches >= min($checks, 2);
    }

    public function submitWithdraw(ClientUser $user, float $amount): array
    {
        if (! $user->isKycVerified()) {
            return ['code' => ErrorCodes::USER_NOT_FULLY_VERIFIED];
        }

        if (empty($user->account_name) || empty($user->bank_number) || empty($user->bank_account)) {
            return ['code' => ErrorCodes::WITHDRAW_BANK_INFO_MISSING];
        }

        if ((float) $user->balance < $amount) {
            return ['code' => ErrorCodes::WITHDRAW_INSUFFICIENT_BALANCE];
        }

        WithdrawRequest::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'processing',
        ]);

        return ['code' => ErrorCodes::WITHDRAW_REQUESTED];
    }

    private function normalize(string $value): string
    {
        $value = trim(mb_strtolower($value));
        // Remove extra spaces
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }
}
