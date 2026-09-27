<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Services\Analytics\PerformanceService;
use App\Services\Market\PriceService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function performance(Request $request, PriceService $prices, PerformanceService $performance)
    {
        $portfolios = auth()->user()->portfolios()->with('assets')->get();

        $result = [];
        foreach ($portfolios as $portfolio) {
            $value = 0.0;
            foreach ($portfolio->assets as $asset) {
                if ((float) $asset->quantity <= 0) {
                    continue;
                }
                $value += (float) $asset->quantity * $prices->getCurrentPrice($asset->symbol);
            }
            $result[] = [
                'id' => $portfolio->id,
                'name' => $portfolio->name,
                'value' => round($value, 2),
                'daily' => $portfolio->dailyPerformance()->orderBy('date')->get(),
            ];
        }

        return response()->json($result);
    }

    public function trades()
    {
        return response()->json(app(PerformanceService::class)->winRate(auth()->id()));
    }

    public function risk()
    {
        return response()->json(auth()->user()->riskRules()->where('status', 'active')->get());
    }

    public function compare(Request $request, PriceService $prices)
    {
        $symbols = $request->get('symbols', ['BTCUSDT', 'ETHUSDT']);

        $result = [];
        foreach ($symbols as $symbol) {
            $result[$symbol] = [
                'ticker' => $prices->getTicker($symbol),
                'history' => $prices->history($symbol, '1h', 24),
            ];
        }

        return response()->json($result);
    }
}
