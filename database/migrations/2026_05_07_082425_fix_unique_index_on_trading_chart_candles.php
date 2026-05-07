<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trading_chart_candles', function (Blueprint $table) {
            $table->dropUnique('uq_candle_symbol_interval_timestamp');
            $table->unique(
                ['symbol', 'interval', 'timeline_type', 'timestamp'],
                'uq_candle_symbol_interval_timeline_ts'
            );
        });
    }

    public function down(): void
    {
        Schema::table('trading_chart_candles', function (Blueprint $table) {
            $table->dropUnique('uq_candle_symbol_interval_timeline_ts');
            $table->unique(
                ['symbol', 'interval', 'timestamp'],
                'uq_candle_symbol_interval_timestamp'
            );
        });
    }
};
