<?php

namespace App\Jobs;

use App\Models\Trade;
use App\Services\Trading\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExecuteTrade implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $tradeId,
    ) {
    }

    public function handle(OrderService $orders): void
    {
        $trade = Trade::find($this->tradeId);
        if (! $trade || $trade->status !== 'pending') {
            return;
        }

        $orders->execute($trade);
    }
}
