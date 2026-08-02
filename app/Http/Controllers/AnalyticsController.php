<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Trade;
use App\Services\Analytics\PerformanceService;
use App\Services\Market\PriceService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request, PriceService $prices, PerformanceService $performance)
    {
        $user = auth()->user();
        $symbol = strtoupper($request->get('symbol', 'BTCUSDT'));

        $stats = $performance->winRate($user->id);

        $history = $prices->history($symbol, '1d', 90);

        $portfolioValues = [];
        $portfolioNames = [];
        foreach ($user->portfolios()->with('assets')->get() as $portfolio) {
            $total = 0.0;
            foreach ($portfolio->assets as $asset) {
                if ((float) $asset->quantity <= 0) {
                    continue;
                }
                $total += (float) $asset->quantity * $prices->getCurrentPrice($asset->symbol);
            }
            $portfolioValues[$portfolio->id] = round($total, 2);
            $portfolioNames[$portfolio->id] = $portfolio->name;
        }

        $recentTrades = Trade::where('user_id', $user->id)
            ->where('status', 'filled')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return view('analytics.index', compact('stats', 'history', 'symbol', 'portfolioValues', 'portfolioNames', 'recentTrades'));
    }
}
