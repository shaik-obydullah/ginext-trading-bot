<?php

namespace App\Services\Strategy;

use App\Services\Analytics\IndicatorService;

class RsiStrategy implements StrategyInterface
{
    public function __construct(
        protected IndicatorService $indicators,
    ) {
    }

    public function name(): string
    {
        return 'RSI Crossover';
    }

    public function type(): string
    {
        return 'rsi_crossover';
    }

    public function shouldBuy(array $params, array $candles): bool
    {
        $prices = array_column($candles, 'close');
        $period = $params['rsi_period'] ?? 14;
        $oversold = $params['oversold'] ?? 30;

        $rsi = $this->indicators->rsi($prices, $period);
        if (count($rsi) < 2) {
            return false;
        }

        $current = end($rsi);
        $previous = $rsi[count($rsi) - 2];

        return $previous < $oversold && $current >= $oversold;
    }

    public function shouldSell(array $params, array $candles): bool
    {
        $prices = array_column($candles, 'close');
        $period = $params['rsi_period'] ?? 14;
        $overbought = $params['overbought'] ?? 70;

        $rsi = $this->indicators->rsi($prices, $period);
        if (count($rsi) < 2) {
            return false;
        }

        $current = end($rsi);
        $previous = $rsi[count($rsi) - 2];

        return $previous > $overbought && $current <= $overbought;
    }

    public function validateParams(array $params): array
    {
        return [
            'rsi_period' => $params['rsi_period'] ?? 14,
            'oversold' => $params['oversold'] ?? 30,
            'overbought' => $params['overbought'] ?? 70,
            'position_size' => $params['position_size'] ?? 0.001,
            'stop_loss' => $params['stop_loss'] ?? 2.0,
            'take_profit' => $params['take_profit'] ?? 5.0,
        ];
    }
}
