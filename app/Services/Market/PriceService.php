<?php

namespace App\Services\Market;

use App\Models\PriceHistory;
use App\Services\Exchange\BinanceService;
use Illuminate\Support\Facades\Cache;

class PriceService
{
    public function __construct(
        protected BinanceService $binance,
    ) {
    }

    public function getCurrentPrice(string $symbol): float
    {
        return Cache::remember("price.{$symbol}", 10, function () use ($symbol) {
            return $this->binance->getTicker($symbol)['price'];
        });
    }

    public function getTicker(string $symbol): array
    {
        return $this->binance->getTicker($symbol);
    }

    public function getTickers(array $symbols = []): array
    {
        $prices = $this->binance->getPrices($symbols);

        $result = [];
        foreach ($prices as $symbol => $price) {
            $result[] = [
                'symbol' => $symbol,
                'price' => $price,
            ];
        }

        return $result;
    }

    public function storeKlines(string $symbol, string $interval = '1h', int $limit = 500): int
    {
        $klines = $this->binance->getKlines($symbol, $interval, $limit);

        $count = 0;
        foreach ($klines as $kline) {
            PriceHistory::updateOrCreate(
                [
                    'symbol' => $symbol,
                    'exchange' => 'binance',
                    'timestamp' => now()->setTimestamp($kline['open_time'])->toDateTimeString(),
                ],
                [
                    'open' => $kline['open'],
                    'high' => $kline['high'],
                    'low' => $kline['low'],
                    'close' => $kline['close'],
                    'volume' => $kline['volume'],
                ]
            );
            $count++;
        }

        return $count;
    }

    public function history(string $symbol, string $interval = '1h', int $limit = 100): array
    {
        $klines = $this->binance->getKlines($symbol, $interval, $limit);

        return array_map(fn ($k) => [
            'time' => $k['open_time'] * 1000,
            'open' => $k['open'],
            'high' => $k['high'],
            'low' => $k['low'],
            'close' => $k['close'],
            'volume' => $k['volume'],
        ], $klines);
    }
}
