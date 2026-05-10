<?php

namespace App\Services\Trading;

use App\Models\ProjectSetting;

class TradingFeeService
{
    /**
     * Get the configured fee percentage (e.g. 5 means 5%).
     */
    public function getFeePercent(): float
    {
        return (float) ProjectSetting::getValue('trading_fee_percent', '5');
    }

    /**
     * Calculate the fee amount from a bet amount.
     */
    public function calculateFee(float $betAmount): float
    {
        $percent = $this->getFeePercent();

        return round($betAmount * ($percent / 100), 2);
    }

    /**
     * Calculate the final payout for a winning trade after fee deduction.
     * Current payout logic: win = amount * 2. Fee is deducted from that.
     */
    public function calculatePayout(float $betAmount): float
    {
        $grossPayout = $betAmount * 2;

        return round($grossPayout - $this->calculateFee($betAmount), 2);
    }
}
