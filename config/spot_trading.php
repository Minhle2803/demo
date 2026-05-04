<?php

return [
    'demo_balances' => [
        'USDT' => '1000000',
        'BTC' => '2',
        'ETH' => '10',
    ],

    'symbols' => [
        'BTC_USDT' => [
            'base_asset' => 'BTC',
            'quote_asset' => 'USDT',
            'price_precision' => 2,
            'quantity_precision' => 8,
            'min_quantity' => '0.00000001',
            'min_notional' => '5',
        ],
        'ETH_USDT' => [
            'base_asset' => 'ETH',
            'quote_asset' => 'USDT',
            'price_precision' => 2,
            'quantity_precision' => 8,
            'min_quantity' => '0.00000001',
            'min_notional' => '5',
        ],
    ],
];
