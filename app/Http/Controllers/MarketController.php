<?php

namespace App\Http\Controllers;

use App\Services\Analytics\IndicatorService;
use App\Services\Market\PriceService;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function index(Request $request, PriceService $prices)
    {
        $symbol = strtoupper($request->get('symbol', 'BTCUSDT'));
        $interval = $request->get('interval', '1h');

        $ticker = $prices->getTicker($symbol);
        $history = $prices->history($symbol, $interval, 200);

        $suggestions = ['BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'SOLUSDT', 'XRPUSDT', 'ADAUSDT', 'DOGEUSDT', 'LINKUSDT'];

        return view('market.index', compact('symbol', 'interval', 'ticker', 'history', 'suggestions'));
    }

    public function chart(Request $request, PriceService $prices)
    {
        $symbol = strtoupper($request->get('symbol', 'BTCUSDT'));
        $interval = $request->get('interval', '1h');

        return response()->json($prices->history($symbol, $interval, 200));
    }

    public function ticker(Request $request, PriceService $prices)
    {
        $symbol = strtoupper($request->get('symbol', 'BTCUSDT'));

        return response()->json($prices->getTicker($symbol));
    }

    public function indicators(Request $request, PriceService $prices, IndicatorService $indicators)
    {
        $symbol = strtoupper($request->get('symbol', 'BTCUSDT'));
        $interval = $request->get('interval', '1h');

        $candles = $prices->history($symbol, $interval, 200);
        $closes = array_column($candles, 'close');

        $rsi = $indicators->rsi($closes, 14);
        $sma20 = $indicators->sma($closes, 20);
        $sma50 = $indicators->sma($closes, 50);
        $macd = $indicators->macd($closes);
        $bollinger = $indicators->bollingerBands($closes);

        return response()->json([
            'symbol' => $symbol,
            'rsi' => $rsi,
            'sma20' => $sma20,
            'sma50' => $sma50,
            'macd' => $macd['macd'],
            'signal' => $macd['signal'],
            'bollinger' => $bollinger,
        ]);
    }
}
