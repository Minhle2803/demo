<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;

class AdminDashboardController extends Controller
{
    public function index(AdminDashboardService $service)
    {
        $stats = $service->getStats();
        $symbols = $service->getSymbols();
        $recentBuyOrders = $service->getRecentSpotBuyOrders(limit: 10);
        $recentSellOrders = $service->getRecentSpotSellOrders(limit: 10);

        return view('pages.admin.dashboard', compact(
            'stats', 'symbols', 'recentBuyOrders', 'recentSellOrders'
        ));
    }
}
