<?php

namespace App\Console\Commands;

use App\Services\TradingChart\ChartSummaryService;
use Illuminate\Console\Command;

class RefreshTradingChartSummary extends Command
{
    protected $signature = 'chart:summary-refresh';
    protected $description = 'Refresh trading chart summary cache table.';

    public function handle(ChartSummaryService $service): int
    {
        $service->refreshAll();

        $this->info('Trading chart summaries refreshed.');

        return self::SUCCESS;
    }
}