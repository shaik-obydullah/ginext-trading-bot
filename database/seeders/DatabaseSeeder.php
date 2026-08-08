<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use App\Models\RiskRule;
use App\Models\Strategy;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );

        $portfolio = Portfolio::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Main Portfolio'],
            [
                'description' => 'Primary automated trading portfolio',
                'default_currency' => 'USDT',
            ]
        );

        $strategies = [
            [
                'user_id' => $user->id,
                'name' => 'RSI Mean Reversion',
                'type' => 'rsi_crossover',
                'symbol' => 'BTCUSDT',
                'timeframe' => '1h',
                'parameters_json' => [
                    'rsi_period' => 14,
                    'oversold' => 30,
                    'overbought' => 70,
                    'position_size' => 0.001,
                    'stop_loss' => 2.0,
                    'take_profit' => 5.0,
                ],
                'status' => 'inactive',
            ],
            [
                'user_id' => $user->id,
                'name' => 'MACD Trend',
                'type' => 'macd_crossover',
                'symbol' => 'ETHUSDT',
                'timeframe' => '4h',
                'parameters_json' => [
                    'fast_period' => 12,
                    'slow_period' => 26,
                    'signal_period' => 9,
                    'position_size' => 0.01,
                    'stop_loss' => 3.0,
                    'take_profit' => 6.0,
                ],
                'status' => 'inactive',
            ],
            [
                'user_id' => $user->id,
                'name' => 'BTC Grid',
                'type' => 'grid',
                'symbol' => 'BTCUSDT',
                'timeframe' => '15m',
                'parameters_json' => [
                    'grid_count' => 10,
                    'grid_step' => 1.0,
                    'position_size' => 0.001,
                ],
                'status' => 'inactive',
            ],
        ];

        foreach ($strategies as $strategy) {
            Strategy::firstOrCreate(
                ['user_id' => $user->id, 'name' => $strategy['name']],
                $strategy
            );
        }

        $rules = [
            ['max_position_size', 1.0],
            ['daily_loss_limit', 100.0],
            ['max_open_orders', 5],
            ['max_exposure', 0.8],
        ];

        foreach ($rules as [$type, $value]) {
            RiskRule::firstOrCreate(
                ['user_id' => $user->id, 'rule_type' => $type],
                [
                    'parameters_json' => [$type => $value],
                    'status' => 'active',
                ]
            );
        }

        $strategyIds = Strategy::where('user_id', $user->id)->pluck('id')->all();

        Trade::where('user_id', $user->id)->delete();

        foreach (range(1, 35) as $i) {
            Trade::factory()->create([
                'user_id' => $user->id,
                'portfolio_id' => $portfolio->id,
                'strategy_id' => $strategyIds[array_rand($strategyIds)],
            ]);
        }
    }
}
