<?php

namespace Database\Factories;

use App\Models\Trade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TradeFactory extends Factory
{
    protected $model = Trade::class;

    public function definition(): array
    {
        $symbol = fake()->randomElement(['BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'SOLUSDT', 'XRPUSDT', 'DOGEUSDT', 'ADAUSDT', 'LINKUSDT', 'AVAXUSDT', 'MATICUSDT']);
        $quantity = fake()->randomFloat(6, 0.001, 50);
        $price = $this->priceFor($symbol);
        $total = round($quantity * $price, 8);
        $status = fake()->randomElement(['filled', 'filled', 'filled', 'filled', 'partial', 'cancelled', 'failed']);
        $executedAt = in_array($status, ['filled', 'partial'])
            ? fake()->dateTimeBetween('-30 days', 'now')
            : null;

        return [
            'user_id' => User::factory(),
            'portfolio_id' => null,
            'strategy_id' => null,
            'exchange' => 'binance',
            'symbol' => $symbol,
            'side' => fake()->randomElement(['buy', 'sell']),
            'type' => fake()->randomElement(['market', 'limit', 'limit', 'stop_loss', 'take_profit']),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $total,
            'fee' => round($total * 0.001, 8),
            'status' => $status,
            'executed_at' => $executedAt,
            'created_at' => $executedAt ?? now(),
            'updated_at' => $executedAt ?? now(),
        ];
    }

    private function priceFor(string $symbol): float
    {
        return match ($symbol) {
            'BTCUSDT' => fake()->randomFloat(2, 60000, 72000),
            'ETHUSDT' => fake()->randomFloat(2, 3000, 3800),
            'BNBUSDT' => fake()->randomFloat(2, 550, 650),
            'SOLUSDT' => fake()->randomFloat(2, 130, 180),
            'XRPUSDT' => fake()->randomFloat(4, 0.50, 0.80),
            'DOGEUSDT' => fake()->randomFloat(4, 0.10, 0.16),
            'ADAUSDT' => fake()->randomFloat(4, 0.35, 0.55),
            'LINKUSDT' => fake()->randomFloat(2, 13, 20),
            'AVAXUSDT' => fake()->randomFloat(2, 24, 38),
            default => fake()->randomFloat(2, 0.50, 1.50),
        };
    }
}
