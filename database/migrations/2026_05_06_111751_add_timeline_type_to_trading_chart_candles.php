<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trading_chart_candles', function (Blueprint $table) {
            $table->enum('timeline_type', ['realtime', 'future'])
                ->default('realtime')
                ->after('is_modified');

            $table->index(
                ['symbol', 'interval', 'timeline_type', 'timestamp'],
                'idx_candle_pair_timeline_ts'
            );
        });
    }

    public function down(): void
    {
        Schema::table('trading_chart_candles', function (Blueprint $table) {
            $table->dropIndex('idx_candle_pair_timeline_ts');
            $table->dropColumn('timeline_type');
        });
    }
};
