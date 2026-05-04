<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trading_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->default('BTC_USDT');
            $table->string('interval')->default('1m');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('lock_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('status', ['open', 'locked', 'closed'])->default('open');
            $table->decimal('open_price', 24, 8)->nullable();
            $table->decimal('close_price', 24, 8)->nullable();
            $table->bigInteger('candle_timestamp')->nullable(); // ms, links to trading_chart_candles
            $table->timestamps();

            $table->index('status');
            $table->index('start_time');
            $table->index(['symbol', 'interval', 'candle_timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_sessions');
    }
};
