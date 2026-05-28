<?php

namespace App\Services;

use App\Models\ClientUser;
use App\Models\WithdrawRequest;
use App\Support\ErrorCodes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Events\BalanceUpdated;

class ProfileService
{
    public function updateProfile(ClientUser $user, array $data): void
    {
        $profileData = [];

        if (! empty($data['account_name'])) {
            $profileData['account_name'] = $data['account_name'];
        }
        if (! empty($data['bank_number'])) {
            $profileData['bank_number'] = $data['bank_number'];
        }
        if (! empty($data['bank_account'])) {
            $profileData['bank_account'] = $data['bank_account'];
        }
        if (! empty($data['full_name'])) {
            $profileData['full_name'] = $data['full_name'];
        }
        if (! empty($data['date_of_birth'])) {
            $profileData['date_of_birth'] = $data['date_of_birth'];
        }
        if (! empty($data['cccd_number'])) {
            $profileData['cccd_number'] = $data['cccd_number'];
        }

        if (! empty($data['kyc_front'])) {
            $profileData['kyc_front_url'] = $this->storeKycFile($user, $data['kyc_front'], 'front');
        }
        if (! empty($data['kyc_back'])) {
            $profileData['kyc_back_url'] = $this->storeKycFile($user, $data['kyc_back'], 'back');
        }

        $user->update($profileData);
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

        // KYC documents saved — admin approval required to set kyc_verified_at
        return ['code' => ErrorCodes::KYC_VERIFIED_SUCCESS];
    }

    private function storeKycFile(ClientUser $user, UploadedFile $file, string $type): string
    {
        $filename = $user->user_id.'_'.$type.'_'.time().'.'.$file->getClientOriginalExtension();

        return Storage::disk('public')->putFileAs('kyc', $file, $filename);
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
        $balance = (float) $user->balance;

        $user->decrement('balance', $amount);


        WithdrawRequest::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'processing',
        ]);
        
        BalanceUpdated::dispatch(
            $user->id,
            $balance - $amount,
            'withdraw',
            $amount,
        );

        return ['code' => ErrorCodes::WITHDRAW_REQUESTED];
    }
}
