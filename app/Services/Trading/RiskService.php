<?php

namespace App\Services\Trading;

use App\Models\RiskRule;
use App\Models\Trade;

class RiskService
{
    public function __construct(
        protected array $defaults = [
            'max_position_size' => 1.0,
            'daily_loss_limit' => 100.0,
            'max_open_orders' => 5,
            'max_exposure' => 0.8,
        ],
    ) {
    }

    public function check(int $userId, float $amount, array $context = []): array
    {
        $rules = $this->rules($userId);

        if ($amount > $this->get($rules, 'max_position_size', $this->defaults['max_position_size'])) {
            return $this->reject('Exceeds max position size');
        }

        $dailyLoss = $this->dailyLoss($userId);
        $dailyLossLimit = $this->get($rules, 'daily_loss_limit', $this->defaults['daily_loss_limit']);
        if ($dailyLoss < -$dailyLossLimit) {
            return $this->reject('Daily loss limit reached');
        }

        $openOrders = Trade::where('user_id', $userId)
            ->whereIn('status', ['pending', 'open', 'partial'])
            ->count();
        $maxOpen = $this->get($rules, 'max_open_orders', $this->defaults['max_open_orders']);
        if ($openOrders >= $maxOpen) {
            return $this->reject('Max open orders reached');
        }

        $maxExposure = $this->get($rules, 'max_exposure', $this->defaults['max_exposure']);
        $exposure = $context['exposure'] ?? 0;
        if ($exposure > $maxExposure) {
            return $this->reject('Portfolio exposure limit');
        }

        return $this->approve();
    }

    protected function rules(int $userId)
    {
        return RiskRule::where('user_id', $userId)
            ->where('status', 'active')
            ->get()
            ->mapWithKeys(fn ($rule) => [$rule->rule_type => $rule->parameters_json])
            ->toArray();
    }

    protected function get(array $rules, string $key, mixed $default): mixed
    {
        return $rules[$key][$key] ?? $default;
    }

    protected function dailyLoss(int $userId): float
    {
        $start = now()->startOfDay();

        return (float) Trade::where('user_id', $userId)
            ->where('side', 'sell')
            ->where('status', 'filled')
            ->where('executed_at', '>=', $start)
            ->sum('total');
    }

    protected function approve(): array
    {
        return ['approved' => true];
    }

    protected function reject(string $reason): array
    {
        return ['approved' => false, 'reason' => $reason];
    }
}
