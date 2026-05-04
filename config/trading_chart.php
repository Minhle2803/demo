<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Symbols
    |--------------------------------------------------------------------------
    */
    'symbols' => [
        'BTC_USDT',
        'ETH_USDT',
        'SOL_USDT',
    ],

    /*
    |--------------------------------------------------------------------------
    | Intervals
    |--------------------------------------------------------------------------
    */
    'intervals' => [
        '1m',
        '5m',
    ],

    /*
    |--------------------------------------------------------------------------
    | Initial seed prices
    | Used by chart:seed when no candle exists yet in the DB.
    |--------------------------------------------------------------------------
    */
    'initial_prices' => [
        'BTC_USDT' => '60000',
        'ETH_USDT' => '3000',
        'SOL_USDT' => '150',
    ],

    'coins' => [
        'BTC_USDT' => [
            'name' => 'Bitcoin',
            'icon' => 'btc',
            'total_supply' => 21000000,
        ],
        'ETH_USDT' => [
            'name' => 'Ethereum',
            'icon' => 'eth',
            'total_supply' => 120000000,
        ],
        'AVAX_USDT' => [
            'name' => 'Avalanche',
            'icon' => 'avax',
            'total_supply' => 720000000,
        ],
        'DOGE_USDT' => [
            'name' => 'Dogecoin',
            'icon' => 'doge',
            'total_supply' => 140000000000,
        ],
        'BNB_USDT' => [
            'name' => 'Binance',
            'icon' => 'bnb',
            'total_supply' => 150000000,
        ],
        'LTC_USDT' => [
            'name' => 'Litecoin',
            'icon' => 'ltc',
            'total_supply' => 84000000,
        ],
    ],

];
