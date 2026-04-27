<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trading_chart_summaries', function (Blueprint $table) {
            $table->id();

            $table->string('symbol');
            $table->string('interval');
            $table->string('range'); // 1H, 7D, 1M, 1Y

            $table->decimal('open_price', 24, 8)->default(0);
            $table->decimal('current_price', 24, 8)->default(0);
            $table->decimal('high', 24, 8)->default(0);
            $table->decimal('low', 24, 8)->default(0);
            $table->decimal('market_volume', 24, 8)->default(0);
            $table->decimal('change_percent', 10, 4)->default(0);

            $table->unsignedBigInteger('from_timestamp')->nullable();
            $table->unsignedBigInteger('to_timestamp')->nullable();

            $table->unsignedBigInteger('open_timestamp')->nullable();
            $table->unsignedBigInteger('high_timestamp')->nullable();
            $table->unsignedBigInteger('low_timestamp')->nullable();
            $table->unsignedBigInteger('last_candle_timestamp')->nullable();

            $table->timestamps();

            $table->unique(['symbol', 'interval', 'range']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_chart_summaries');
    }
};