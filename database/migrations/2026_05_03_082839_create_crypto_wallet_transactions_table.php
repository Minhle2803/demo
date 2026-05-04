<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('client_users')->cascadeOnDelete();
            $table->string('asset', 20);
            $table->string('type', 30);
            $table->decimal('amount', 36, 18);
            $table->decimal('balance_before', 36, 18);
            $table->decimal('balance_after', 36, 18);
            $table->decimal('locked_before', 36, 18);
            $table->decimal('locked_after', 36, 18);
            $table->string('reference_type', 30)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_wallet_transactions');
    }
};
