<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_trades', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 20);
            $table->foreignId('buy_order_id')->nullable()->constrained('crypto_orders')->nullOnDelete();
            $table->foreignId('sell_order_id')->nullable()->constrained('crypto_orders')->nullOnDelete();
            $table->foreignId('buyer_user_id')->nullable()->constrained('client_users')->nullOnDelete();
            $table->foreignId('seller_user_id')->nullable()->constrained('client_users')->nullOnDelete();
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('source', ['auto_match', 'admin_manual']);
            $table->decimal('price', 36, 18);
            $table->decimal('quantity', 36, 18);
            $table->decimal('total', 36, 18);
            $table->timestamp('created_at')->useCurrent();

            $table->index('symbol');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_trades');
    }
};
