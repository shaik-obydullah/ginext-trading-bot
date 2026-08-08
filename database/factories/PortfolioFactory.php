<?php

namespace Database\Factories;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PortfolioFactory extends Factory
{
    protected $model = Portfolio::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->randomElement([
                'Main Portfolio', 'Growth Portfolio', 'HODL Portfolio', 'Swing Account', 'Scalping Account',
            ]),
            'description' => fake()->sentence(),
            'default_currency' => 'USDT',
            'status' => 'active',
        ];
    }
}
