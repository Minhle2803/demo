<?php

if (! function_exists('format_currency_short')) {
    /**
     * Format a number with abbreviation for large values.
     * - < 1,000: plain number with commas
     * - >= 1,000: X.Xk (e.g. 1,500 → 1.5k)
     * - >= 1,000,000: X.XM (e.g. 2,500,000 → 2.5M)
     * - >= 1,000,000,000: X.XB (e.g. 3,500,000,000 → 3.5B)
     */
    function format_currency_short(float $amount, string $suffix = ''): string
    {
        $sign = $amount < 0 ? '-' : '';
        $amount = abs($amount);

        // if ($amount >= 1_000_000_000) {
        //     $formatted = number_format($amount / 1_000_000_000, 1);
        //     $formatted = rtrim(rtrim($formatted, '0'), '.');
        //     $result = $sign.$formatted.'B';
        // } elseif ($amount >= 1_000_000) {
        //     $formatted = number_format($amount / 1_000_000, 1);
        //     $formatted = rtrim(rtrim($formatted, '0'), '.');
        //     $result = $sign.$formatted.'M';
        // } elseif ($amount >= 1_000) {
        //     $formatted = number_format($amount / 1_000, 1);
        //     $formatted = rtrim(rtrim($formatted, '0'), '.');
        //     $result = $sign.$formatted.'k';
        // } else {
        $result = $sign.number_format($amount, 0);
        // }

        return $suffix ? $result.' '.$suffix : $result;
    }
}
