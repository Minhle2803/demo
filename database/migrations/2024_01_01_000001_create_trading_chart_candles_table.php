<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trading_chart_candles', function (Blueprint $table) {

            // ----------------------------------------------------------------
            // Primary key
            // ----------------------------------------------------------------
            $table->id();

            // ----------------------------------------------------------------
            // Identifiers
            // ----------------------------------------------------------------
            $table->string('symbol', 20)
                ->comment('Trading pair symbol — e.g. BTC_USDT, ETH_USDT, SOL_USDT');

            $table->string('interval', 10)
                ->comment('Candle interval — e.g. 1m, 5m, 15m, 1h, 4h, 1d');

            // ----------------------------------------------------------------
            // Candle timing
            // Stored as milliseconds (Unix epoch × 1000) to match KLineCharts
            // ----------------------------------------------------------------
            $table->unsignedBigInteger('timestamp')
                ->comment('Candle open time in milliseconds — e.g. 1710000000000');

            // ----------------------------------------------------------------
            // OHLCV price fields
            // decimal(24,8) matches crypto precision standards
            // ----------------------------------------------------------------
            $table->decimal('open', 24, 8)
                ->comment('Candle open price');

            $table->decimal('high', 24, 8)
                ->comment('Candle high price — must be >= open and >= close');

            $table->decimal('low', 24, 8)
                ->comment('Candle low price — must be <= open and <= close');

            $table->decimal('close', 24, 8)
                ->comment('Candle close price');

            $table->decimal('volume', 24, 8)
                ->comment('Candle trade volume');

            // ----------------------------------------------------------------
            // Generation control fields
            // ----------------------------------------------------------------
            $table->enum('direction', ['up', 'down', 'neutral'])
                ->default('neutral')
                ->comment('Price movement direction applied during generation');

            $table->enum('status', ['open', 'closed'])
                ->default('open')
                ->comment('open = current live candle being updated; closed = finalized candle');

            $table->boolean('is_generated')
                ->default(true)
                ->comment('True for all system-generated fake candles');

            $table->boolean('is_modified')
                ->default(false)
                ->comment('True if this candle was overwritten by the rewrite-range API');

            // ----------------------------------------------------------------
            // Timestamps
            // ----------------------------------------------------------------
            $table->timestamps();

            // ----------------------------------------------------------------
            // Unique constraint
            // Prevents duplicate candles for the same symbol + interval + time
            // The generator must use upsert — this is the integrity guard
            // ----------------------------------------------------------------
            $table->unique(['symbol', 'interval', 'timestamp'], 'uq_candle_symbol_interval_timestamp');

            // ----------------------------------------------------------------
            // Indexes
            // Optimise the most common query patterns:
            //   - fetch candles by symbol + interval + time range
            //   - find the latest candle per symbol+interval (generator resume)
            //   - filter by status (find the current open candle)
            // ----------------------------------------------------------------

            // Generator resume: ORDER BY timestamp DESC LIMIT 1
            $table->index(['symbol', 'interval', 'timestamp'], 'idx_candle_symbol_interval_timestamp');

            // Status filter: WHERE status = 'open'
            $table->index('status', 'idx_candle_status');

            // Housekeeping / bulk queries by symbol alone
            $table->index('symbol', 'idx_candle_symbol');

            // Housekeeping / bulk queries by interval alone
            $table->index('interval', 'idx_candle_interval');

            // Range queries by raw timestamp (e.g. rewrite-range API)
            $table->index('timestamp', 'idx_candle_timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_chart_candles');
    }
};
