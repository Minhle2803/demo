<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_users', function (Blueprint $table) {
            $table->string('full_name')->nullable()->after('phone_number');
            $table->date('date_of_birth')->nullable()->after('full_name');
            $table->string('cccd_number')->nullable()->after('date_of_birth');
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_back_url');
        });
    }

    public function down(): void
    {
        Schema::table('client_users', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'date_of_birth', 'cccd_number', 'kyc_verified_at']);
        });
    }
};
