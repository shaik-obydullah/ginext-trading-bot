<?php

namespace App\Jobs;

use App\Services\Analytics\IndicatorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateIndicators implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public array $symbols,
    ) {
    }

    public function handle(IndicatorService $indicators): void
    {
        foreach ($this->symbols as $symbol) {
            try {
                $indicators->calculateAndStore($symbol);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
