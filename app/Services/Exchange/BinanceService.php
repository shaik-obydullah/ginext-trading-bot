<?php

namespace App\Services\Exchange;

use Illuminate\Support\Facades\Http;

class BinanceService implements ExchangeInterface
{
    public function __construct(
        protected string $baseUrl = 'https://api.binance.com',
        protected ?string $apiKey = null,
        protected ?string $apiSecret = null,
    ) {
    }

    public function getTicker(string $symbol): array
    {
        $data = $this->public('/api/v3/ticker/24hr', ['symbol' => $symbol]);

        return [
            'symbol' => $symbol,
            'price' => (float) ($data['lastPrice'] ?? 0),
            'high' => (float) ($data['highPrice'] ?? 0),
            'low' => (float) ($data['lowPrice'] ?? 0),
            'change' => (float) ($data['priceChange'] ?? 0),
            'change_percent' => (float) ($data['priceChangePercent'] ?? 0),
            'volume' => (float) ($data['volume'] ?? 0),
        ];
    }

    public function getPrices(array $symbols = []): array
    {
        $data = $this->public('/api/v3/ticker/price');

        $prices = [];
        foreach ($data as $ticker) {
            $prices[$ticker['symbol']] = (float) $ticker['price'];
        }

        if (! empty($symbols)) {
            $prices = array_intersect_key($prices, array_flip($symbols));
        }

        return $prices;
    }

    public function getKlines(string $symbol, string $interval = '1h', int $limit = 100): array
    {
        $data = $this->public('/api/v3/klines', [
            'symbol' => $symbol,
            'interval' => $interval,
            'limit' => $limit,
        ]);

        return array_map(fn ($k) => [
            'open_time' => (int) ($k[0] / 1000),
            'open' => (float) $k[1],
            'high' => (float) $k[2],
            'low' => (float) $k[3],
            'close' => (float) $k[4],
            'volume' => (float) $k[5],
            'close_time' => (int) ($k[6] / 1000),
        ], $data);
    }

    public function getAccountInfo(): array
    {
        return $this->signed('/api/v3/account');
    }

    protected function public(string $path, array $query = []): array
    {
        $response = Http::timeout(10)
            ->retry(2, 300)
            ->get($this->baseUrl.$path, $query);

        $response->throw();

        return $response->json();
    }

    protected function signed(string $path, array $query = []): array
    {
        if (! $this->apiKey || ! $this->apiSecret) {
            throw new \RuntimeException('Binance API credentials are not configured.');
        }

        $query['timestamp'] = (int) (microtime(true) * 1000);
        $query['recvWindow'] = 5000;
        $query['signature'] = hash_hmac('sha256', http_build_query($query), $this->apiSecret);

        $response = Http::timeout(10)
            ->retry(2, 300)
            ->withHeaders(['X-MBX-APIKEY' => $this->apiKey])
            ->get($this->baseUrl.$path, $query);

        $response->throw();

        return $response->json();
    }
}
