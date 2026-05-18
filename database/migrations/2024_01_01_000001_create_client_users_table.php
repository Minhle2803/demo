<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_users', function (Blueprint $table) {
            // Primary key & business identifier
            $table->id();
            $table->string('user_id')->unique()->comment('Auto-generated business identifier, e.g. USR-XXXXXXXX');

            // Core auth fields
            $table->string('email')->nullable();
            $table->string('nickname')->unique();
            $table->string('password');
            $table->string('phone_number')->unique();

            // Verification state
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();

            // Email verification (custom token flow)
            $table->string('email_verification_token')->nullable()->index();

            // Phone OTP
            $table->string('phone_otp_code')->nullable()->comment('Stored as hashed value');
            $table->timestamp('phone_otp_expired_at')->nullable();

            // Referral (code entered by the registering user, not self-generated)
            $table->string('referral_code')->nullable()->comment('Referral code entered by user during registration');

            // Bank information
            $table->string('account_name')->nullable()->comment('Bank account owner name');
            $table->string('bank_account')->nullable()->comment('Bank name');
            $table->string('bank_number')->nullable()->comment('Bank account number');

            // KYC documents
            $table->string('kyc_front_url')->nullable()->comment('Front side of national ID');
            $table->string('kyc_back_url')->nullable()->comment('Back side of national ID');

            // Trading
            $table->string('trading_account')->nullable()->comment('External trading account ID');

            // Wallet balances — stored as high-precision decimal
            $table->decimal('balance', 18, 2)->default(0)->comment('Main wallet balance');
            $table->decimal('trading_balance', 18, 2)->default(0)->comment('Trading balance; must never exceed balance');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_users');
    }
};
