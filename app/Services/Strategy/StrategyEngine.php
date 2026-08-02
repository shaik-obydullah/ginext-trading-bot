<?php

namespace App\Services\Strategy;

use App\Models\Strategy;
use App\Services\Analytics\IndicatorService;

class StrategyEngine
{
    public function __construct(
        protected IndicatorService $indicators,
    ) {
    }

    public function resolve(string $type): StrategyInterface
    {
        return match ($type) {
            'rsi_crossover' => new RsiStrategy($this->indicators),
            'macd_crossover' => new MacdStrategy($this->indicators),
            'grid' => new GridStrategy($this->indicators),
            'dca' => new DcaStrategy($this->indicators),
            default => throw new \InvalidArgumentException("Unknown strategy type: {$type}"),
        };
    }

    public function evaluate(Strategy $strategy, array $candles): string
    {
        $instance = $this->resolve($strategy->type);
        $params = $strategy->parameters_json ?: [];

        if ($instance->shouldBuy($params, $candles)) {
            return 'buy';
        }

        if ($instance->shouldSell($params, $candles)) {
            return 'sell';
        }

        return 'hold';
    }
}
