<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('client_users')->cascadeOnDelete();
            $table->string('symbol', 20);
            $table->string('base_asset', 10);
            $table->string('quote_asset', 10);
            $table->enum('side', ['buy', 'sell']);
            $table->enum('type', ['limit'])->default('limit');
            $table->decimal('price', 36, 18);
            $table->decimal('quantity', 36, 18);
            $table->decimal('filled_quantity', 36, 18)->default('0');
            $table->decimal('remaining_quantity', 36, 18);
            $table->decimal('total_amount', 36, 18);
            $table->enum('status', ['open', 'partially_filled', 'filled', 'cancelled'])->default('open');
            $table->timestamps();

            $table->index('symbol');
            $table->index('side');
            $table->index('status');
            $table->index('price');
            $table->index('created_at');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_orders');
    }
};
