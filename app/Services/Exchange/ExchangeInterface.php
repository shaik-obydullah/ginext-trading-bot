<?php

namespace App\Services\Exchange;

interface ExchangeInterface
{
    public function getTicker(string $symbol): array;

    public function getPrices(array $symbols = []): array;

    public function getKlines(string $symbol, string $interval = '1h', int $limit = 100): array;

    public function getAccountInfo(): array;
}
