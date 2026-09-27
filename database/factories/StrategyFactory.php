<?php

namespace Database\Factories;

use App\Models\Strategy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StrategyFactory extends Factory
{
    protected $model = Strategy::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['rsi_crossover', 'macd_crossover', 'grid', 'dca']);

        $parameters = match ($type) {
            'rsi_crossover' => [
                'rsi_period' => fake()->numberBetween(9, 21),
                'oversold' => fake()->numberBetween(25, 35),
                'overbought' => fake()->numberBetween(65, 75),
                'position_size' => fake()->randomFloat(4, 0.001, 0.05),
                'stop_loss' => fake()->randomFloat(1, 1, 5),
                'take_profit' => fake()->randomFloat(1, 3, 10),
            ],
            'macd_crossover' => [
                'fast_period' => 12,
                'slow_period' => 26,
                'signal_period' => 9,
                'position_size' => fake()->randomFloat(4, 0.001, 0.05),
                'stop_loss' => fake()->randomFloat(1, 1, 5),
                'take_profit' => fake()->randomFloat(1, 3, 10),
            ],
            'grid' => [
                'grid_count' => fake()->numberBetween(5, 20),
                'grid_step' => fake()->randomFloat(1, 0.5, 3),
                'position_size' => fake()->randomFloat(4, 0.001, 0.05),
            ],
            'dca' => [
                'interval' => fake()->randomElement(['1d', '1w']),
                'amount' => fake()->randomFloat(2, 10, 200),
                'stop_loss' => fake()->randomFloat(1, 1, 5),
            ],
        };

        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->randomElement([
                'RSI Reversal', 'MACD Momentum', 'Grid Bot', 'DCA Accumulator', 'Trend Follower',
            ]),
            'type' => $type,
            'symbol' => fake()->randomElement(['BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'SOLUSDT', 'XRPUSDT', 'DOGEUSDT']),
            'timeframe' => fake()->randomElement(['5m', '15m', '1h', '4h']),
            'parameters_json' => $parameters,
            'status' => fake()->randomElement(['active', 'inactive', 'inactive', 'inactive']),
        ];
    }
}
