<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class ClientUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // -------------------------------------------------------------------------
    // Table & guard
    // -------------------------------------------------------------------------

    protected $table = 'client_users';

    /**
     * The guard used when authenticating this model.
     * Matches the guard name defined in config/auth.php.
     */
    protected string $guard = 'client';

    // -------------------------------------------------------------------------
    // Mass-assignment whitelist (security best practice — never use $guarded=[])
    // -------------------------------------------------------------------------

    protected $fillable = [
        'user_id',
        'email',
        'nickname',
        'password',
        'phone_number',
        'is_verified',
        'verified_at',
        'email_verification_token',
        'phone_otp_code',
        'phone_otp_expired_at',
        'referral_code',
        'account_name',
        'bank_account',
        'bank_number',
        'kyc_front_url',
        'kyc_back_url',
        'trading_account',
        'balance',
        'trading_balance',
        'full_name',
        'date_of_birth',
        'cccd_number',
        'kyc_verified_at',
    ];

    // -------------------------------------------------------------------------
    // Hidden attributes (never exposed in JSON / serialization)
    // -------------------------------------------------------------------------

    protected $hidden = [
        'password',
        'email_verification_token',
        'phone_otp_code',
        'phone_otp_expired_at',
    ];

    // -------------------------------------------------------------------------
    // Casts
    // -------------------------------------------------------------------------

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'phone_otp_expired_at' => 'datetime',
            'balance' => 'decimal:2',
            'trading_balance' => 'decimal:2',
            'date_of_birth' => 'date',
            'kyc_verified_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Boot — auto-generate user_id on creation
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            if (empty($user->user_id)) {
                $user->user_id = 'USR-'.strtoupper(Str::random(8));
            }
        });
    }

    // -------------------------------------------------------------------------
    // Verification helpers
    // -------------------------------------------------------------------------

    /**
     * Whether the client user's email has been verified.
     * Uses the presence of email_verification_token being null as the signal.
     * If you use Laravel's MustVerifyEmail contract, adapt accordingly.
     */
    public function hasVerifiedEmail(): bool
    {
        // A null token means the email has been confirmed and the token consumed.
        return $this->email_verification_token === null;
    }

    public function hasVerifiedPhone(): bool
    {
        // Phone is considered verified once is_verified considers both factors,
        // but we track intermediate state via a separate boolean not stored in DB.
        // The OtpService marks this by clearing the OTP fields.
        return $this->phone_otp_code === null && $this->phone_otp_expired_at === null;
    }

    /**
     * Mark both email and phone as fully verified and set verified_at.
     * Must only be called after confirming both verifications.
     */
    public function markFullyVerified(): void
    {
        $this->forceFill([
            'is_verified' => true,
            'verified_at' => now(),
        ])->save();
    }

    // -------------------------------------------------------------------------
    // Balance guard helper
    // -------------------------------------------------------------------------

    public function canSetTradingBalance(float|string $amount): bool
    {
        return bccomp((string) $amount, (string) $this->balance, 2) <= 0;
    }

    public function isKycVerified(): bool
    {
        return $this->kyc_verified_at !== null
            && ! empty($this->kyc_front_url)
            && ! empty($this->kyc_back_url);
    }
}
