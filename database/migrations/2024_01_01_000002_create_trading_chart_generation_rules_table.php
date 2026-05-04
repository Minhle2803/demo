<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trading_chart_generation_rules', function (Blueprint $table) {

            $table->id();

            // ----------------------------------------------------------------
            // Target pair
            // ----------------------------------------------------------------
            $table->string('symbol', 20)
                ->comment('e.g. BTC_USDT');

            $table->string('interval', 10)
                ->comment('e.g. 1m, 5m, 1h');

            // ----------------------------------------------------------------
            // Rule definition
            // ----------------------------------------------------------------
            $table->enum('forced_direction', ['up', 'down', 'neutral'])
                ->default('neutral')
                ->comment('Direction bias applied to candle generation');

            $table->decimal('price_bias_percent', 8, 4)
                ->default(0)
                ->comment('Additional % bias per tick. Positive = up, negative = down');

            // ----------------------------------------------------------------
            // Time window (milliseconds, nullable = open-ended)
            // ----------------------------------------------------------------
            $table->unsignedBigInteger('active_from')
                ->nullable()
                ->comment('Rule applies from this timestamp (ms). NULL = immediately');

            $table->unsignedBigInteger('active_until')
                ->nullable()
                ->comment('Rule expires after this timestamp (ms). NULL = indefinitely');

            // ----------------------------------------------------------------
            // Flags
            // ----------------------------------------------------------------
            $table->boolean('apply_to_existing')
                ->default(false)
                ->comment('If true, rewrite existing candles in the time window on creation');

            $table->boolean('is_active')
                ->default(true)
                ->comment('Soft toggle. False = rule is ignored by the worker');

            $table->timestamps();

            // ----------------------------------------------------------------
            // Indexes
            // ----------------------------------------------------------------

            // Worker lookup: active rules for a pair covering a timestamp
            $table->index(['symbol', 'interval', 'is_active'], 'idx_rule_pair_active');

            // Time window queries
            $table->index('active_from', 'idx_rule_active_from');
            $table->index('active_until', 'idx_rule_active_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_chart_generation_rules');
    }
};
