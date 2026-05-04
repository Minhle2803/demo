<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('client_users')->cascadeOnDelete();
            $table->string('asset', 20);
            $table->decimal('available_balance', 36, 18)->default('0');
            $table->decimal('locked_balance', 36, 18)->default('0');
            $table->timestamps();

            $table->unique(['user_id', 'asset']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_wallets');
    }
};
