<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_assets', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 20)->unique();
            $table->string('name');
            $table->string('icon_url')->nullable();
            $table->string('base_asset', 10);
            $table->string('quote_asset', 10);
            $table->decimal('price', 36, 18)->nullable();
            $table->unsignedTinyInteger('price_precision')->default(2);
            $table->unsignedTinyInteger('quantity_precision')->default(8);
            $table->decimal('min_quantity', 36, 18)->default('0.00000001');
            $table->decimal('min_notional', 36, 18)->default('10.00');
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_assets');
    }
};
