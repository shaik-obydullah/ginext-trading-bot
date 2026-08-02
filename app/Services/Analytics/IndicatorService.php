<?php

namespace App\Services\Analytics;

use App\Models\Indicator;
use App\Models\PriceHistory;

class IndicatorService
{
    public function rsi(array $prices, int $period = 14): array
    {
        $rsi = [];
        if (count($prices) <= $period) {
            return $rsi;
        }

        $gains = 0.0;
        $losses = 0.0;

        for ($i = 1; $i <= $period; $i++) {
            $change = $prices[$i] - $prices[$i - 1];
            if ($change >= 0) {
                $gains += $change;
            } else {
                $losses += abs($change);
            }
        }

        $avgGain = $gains / $period;
        $avgLoss = $losses / $period;

        for ($i = $period + 1; $i < count($prices); $i++) {
            $change = $prices[$i] - $prices[$i - 1];
            $gain = max($change, 0);
            $loss = abs(min($change, 0));

            $avgGain = (($avgGain * ($period - 1)) + $gain) / $period;
            $avgLoss = (($avgLoss * ($period - 1)) + $loss) / $period;

            $rs = $avgLoss == 0 ? 100 : $avgGain / $avgLoss;
            $rsi[] = 100 - (100 / (1 + $rs));
        }

        return $rsi;
    }

    public function sma(array $prices, int $period): array
    {
        $result = [];
        $sum = 0.0;

        foreach ($prices as $i => $price) {
            $sum += $price;
            if ($i >= $period) {
                $sum -= $prices[$i - $period];
            }
            if ($i >= $period - 1) {
                $result[] = $sum / $period;
            }
        }

        return $result;
    }

    public function ema(array $prices, int $period): array
    {
        $result = [];
        if (count($prices) < $period) {
            return $result;
        }

        $multiplier = 2 / ($period + 1);
        $sma = array_sum(array_slice($prices, 0, $period)) / $period;
        $result[] = $sma;

        for ($i = $period; $i < count($prices); $i++) {
            $ema = (($prices[$i] - $sma) * $multiplier) + $sma;
            $result[] = $ema;
            $sma = $ema;
        }

        return $result;
    }

    public function macd(array $prices, int $fast = 12, int $slow = 26, int $signalPeriod = 9): array
    {
        $emaFast = $this->ema($prices, $fast);
        $emaSlow = $this->ema($prices, $slow);

        $diff = abs(count($emaFast) - count($emaSlow));
        $macdLine = [];
        $count = min(count($emaFast), count($emaSlow));

        for ($i = 0; $i < $count; $i++) {
            $macdLine[] = $emaFast[$i + $diff] - $emaSlow[$i];
        }

        $signal = $this->ema($macdLine, $signalPeriod);

        return [
            'macd' => $macdLine,
            'signal' => $signal,
        ];
    }

    public function bollingerBands(array $prices, int $period = 20, float $stdDev = 2.0): array
    {
        $middle = $this->sma($prices, $period);
        $upper = [];
        $lower = [];

        $offset = $period - 1;
        foreach ($middle as $i => $mean) {
            $slice = array_slice($prices, $offset + $i - ($period - 1), $period);
            $variance = array_sum(array_map(fn ($p) => ($p - $mean) ** 2, $slice)) / $period;
            $sd = sqrt($variance);
            $upper[] = $mean + ($stdDev * $sd);
            $lower[] = $mean - ($stdDev * $sd);
        }

        return [
            'middle' => $middle,
            'upper' => $upper,
            'lower' => $lower,
        ];
    }

    public function calculateAndStore(string $symbol, int $period = 14): void
    {
        $prices = PriceHistory::where('symbol', $symbol)
            ->orderBy('timestamp')
            ->pluck('close')
            ->toArray();

        if (count($prices) < $period + 1) {
            return;
        }

        $rsiSeries = $this->rsi($prices, $period);
        if (! empty($rsiSeries)) {
            Indicator::updateOrCreate(
                [
                    'symbol' => $symbol,
                    'indicator_type' => 'rsi',
                    'period' => $period,
                    'calculated_at' => now(),
                ],
                ['value' => end($rsiSeries)]
            );
        }
    }
}
