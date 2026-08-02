<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Strategy;
use App\Models\Trade;
use App\Services\Analytics\PerformanceService;
use App\Services\Market\PriceService;

class DashboardController extends Controller
{
    public function index(PriceService $prices, PerformanceService $performance)
    {
        $user = auth()->user();

        $portfolios = $user->portfolios()->with('assets')->get();
        $strategies = $user->strategies()->get();
        $trades = Trade::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $symbols = $strategies->pluck('symbol')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($symbols)) {
            $symbols = ['BTCUSDT', 'ETHUSDT', 'BNBUSDT'];
        }

        $tickers = [];
        foreach ($symbols as $symbol) {
            try {
                $tickers[] = $prices->getTicker($symbol);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $stats = $performance->winRate($user->id);

        return view('dashboard.index', compact('portfolios', 'strategies', 'trades', 'tickers', 'stats'));
    }
}
