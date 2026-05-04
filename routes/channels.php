<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chart.{symbol}.{interval}', function () {
    return true; // open to all connections — internal tool only
});

Broadcast::channel('spot-orderbook.{symbol}', function () {
    return true; // open to all connections — public data
});
