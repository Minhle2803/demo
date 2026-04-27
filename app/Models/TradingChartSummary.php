<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingChartSummary extends Model
{
    protected $fillable = [
        'symbol',
        'interval',
        'range',
        'open_price',
        'current_price',
        'high',
        'low',
        'market_volume',
        'change_percent',
        'from_timestamp',
        'to_timestamp',
        'open_timestamp',
        'high_timestamp',
        'low_timestamp',
        'last_candle_timestamp',
    ];
}