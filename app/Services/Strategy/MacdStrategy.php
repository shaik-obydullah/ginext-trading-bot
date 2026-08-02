<?php

namespace App\Services\Strategy;

use App\Services\Analytics\IndicatorService;

class MacdStrategy implements StrategyInterface
{
    public function __construct(
        protected IndicatorService $indicators,
    ) {
    }

    public function name(): string
    {
        return 'MACD Crossover';
    }

    public function type(): string
    {
        return 'macd_crossover';
    }

    public function shouldBuy(array $params, array $candles): bool
    {
        $prices = array_column($candles, 'close');
        $fast = $params['fast_period'] ?? 12;
        $slow = $params['slow_period'] ?? 26;
        $signalPeriod = $params['signal_period'] ?? 9;

        $macd = $this->indicators->macd($prices, $fast, $slow, $signalPeriod);
        if (count($macd['macd']) < 2 || count($macd['signal']) < 2) {
            return false;
        }

        $currentMacd = end($macd['macd']);
        $currentSignal = end($macd['signal']);
        $previousMacd = $macd['macd'][count($macd['macd']) - 2];
        $previousSignal = $macd['signal'][count($macd['signal']) - 2];

        return $previousMacd <= $previousSignal && $currentMacd > $currentSignal;
    }

    public function shouldSell(array $params, array $candles): bool
    {
        $prices = array_column($candles, 'close');
        $fast = $params['fast_period'] ?? 12;
        $slow = $params['slow_period'] ?? 26;
        $signalPeriod = $params['signal_period'] ?? 9;

        $macd = $this->indicators->macd($prices, $fast, $slow, $signalPeriod);
        if (count($macd['macd']) < 2 || count($macd['signal']) < 2) {
            return false;
        }

        $currentMacd = end($macd['macd']);
        $currentSignal = end($macd['signal']);
        $previousMacd = $macd['macd'][count($macd['macd']) - 2];
        $previousSignal = $macd['signal'][count($macd['signal']) - 2];

        return $previousMacd >= $previousSignal && $currentMacd < $currentSignal;
    }

    public function validateParams(array $params): array
    {
        return [
            'fast_period' => $params['fast_period'] ?? 12,
            'slow_period' => $params['slow_period'] ?? 26,
            'signal_period' => $params['signal_period'] ?? 9,
            'position_size' => $params['position_size'] ?? 0.001,
            'stop_loss' => $params['stop_loss'] ?? 2.0,
            'take_profit' => $params['take_profit'] ?? 5.0,
        ];
    }
}
