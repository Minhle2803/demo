<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('client_users', function (Blueprint $table) {
            $table->string('invite_code', 11)->unique()->nullable()->after('referral_code');
            $table->unsignedBigInteger('invited_by_admin_id')->nullable()->after('invite_code');
            $table->unsignedBigInteger('invited_by_client_id')->nullable()->after('invited_by_admin_id');

            $table->foreign('invited_by_admin_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('invited_by_client_id')->references('id')->on('client_users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_users', function (Blueprint $table) {
            $table->dropForeign(['invited_by_admin_id']);
            $table->dropForeign(['invited_by_client_id']);
            $table->dropUnique(['invite_code']);
            $table->dropColumn(['invite_code', 'invited_by_admin_id', 'invited_by_client_id']);
        });
    }
};
