<?php

namespace App\Jobs;

use App\Services\Market\PriceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdatePrices implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public array $symbols,
        public string $interval = '1h',
    ) {
    }

    public function handle(PriceService $prices): void
    {
        foreach ($this->symbols as $symbol) {
            try {
                $prices->storeKlines($symbol, $this->interval, 50);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
