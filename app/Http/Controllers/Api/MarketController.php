<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Analytics\IndicatorService;
use App\Services\Market\PriceService;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function __construct(
        protected PriceService $prices,
        protected IndicatorService $indicators,
    ) {
    }

    public function prices(string $symbol)
    {
        return response()->json($this->prices->getTicker(strtoupper($symbol)));
    }

    public function history(Request $request, string $symbol)
    {
        $interval = $request->get('interval', '1h');

        return response()->json($this->prices->history(strtoupper($symbol), $interval, 200));
    }

    public function indicators(Request $request, string $symbol)
    {
        $interval = $request->get('interval', '1h');
        $closes = array_column($this->prices->history(strtoupper($symbol), $interval, 200), 'close');

        return response()->json([
            'symbol' => strtoupper($symbol),
            'rsi' => $this->indicators->rsi($closes, 14),
            'sma20' => $this->indicators->sma($closes, 20),
            'sma50' => $this->indicators->sma($closes, 50),
            'macd' => $this->indicators->macd($closes),
            'bollinger' => $this->indicators->bollingerBands($closes),
        ]);
    }

    public function ticker(Request $request)
    {
        $symbols = $request->get('symbols', []);

        return response()->json($this->prices->getTickers($symbols));
    }
}
