<?php

namespace App\Services\Strategy;

class DcaStrategy implements StrategyInterface
{
    public function name(): string
    {
        return 'Dollar Cost Averaging';
    }

    public function type(): string
    {
        return 'dca';
    }

    public function shouldBuy(array $params, array $candles): bool
    {
        return false;
    }

    public function shouldSell(array $params, array $candles): bool
    {
        return false;
    }

    public function validateParams(array $params): array
    {
        return [
            'interval' => $params['interval'] ?? '1d',
            'amount_per_trade' => $params['amount_per_trade'] ?? 50,
            'position_size' => $params['position_size'] ?? 0.001,
        ];
    }
}
