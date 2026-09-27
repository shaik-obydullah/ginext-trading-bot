<?php

namespace App\Services\Strategy;

class GridStrategy implements StrategyInterface
{
    public function name(): string
    {
        return 'Grid Trading';
    }

    public function type(): string
    {
        return 'grid';
    }

    public function shouldBuy(array $params, array $candles): bool
    {
        $current = $this->currentPrice($candles);
        $gridStep = $params['grid_step'] ?? 1.0;

        $target = ($current - $gridStep) + $gridStep;

        return $current <= $target;
    }

    public function shouldSell(array $params, array $candles): bool
    {
        return false;
    }

    public function validateParams(array $params): array
    {
        return [
            'lower_bound' => $params['lower_bound'] ?? 0,
            'upper_bound' => $params['upper_bound'] ?? 0,
            'grid_count' => $params['grid_count'] ?? 10,
            'grid_step' => $params['grid_step'] ?? 1.0,
            'position_size' => $params['position_size'] ?? 0.001,
        ];
    }

    protected function currentPrice(array $candles): float
    {
        $candle = end($candles);

        return (float) ($candle['close'] ?? 0);
    }
}
