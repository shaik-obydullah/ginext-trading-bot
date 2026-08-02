<?php

namespace App\Jobs;

use App\Models\StopLossOrder;
use App\Models\Trade;
use App\Services\Market\PriceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckStopLoss implements ShouldQueue
{
    use Queueable;

    public function handle(PriceService $prices): void
    {
        $stopLosses = StopLossOrder::where('status', 'pending')->with('trade')->get();

        foreach ($stopLosses as $stopLoss) {
            $trade = $stopLoss->trade;
            if (! $trade) {
                continue;
            }

            try {
                $currentPrice = $prices->getCurrentPrice($trade->symbol);
            } catch (\Throwable $e) {
                report($e);
                continue;
            }

            $triggered = $trade->side === 'buy'
                ? $currentPrice <= (float) $stopLoss->trigger_price
                : $currentPrice >= (float) $stopLoss->trigger_price;

            if ($triggered) {
                Trade::create([
                    'portfolio_id' => $trade->portfolio_id,
                    'strategy_id' => $trade->strategy_id,
                    'user_id' => $trade->user_id,
                    'exchange' => $trade->exchange,
                    'symbol' => $trade->symbol,
                    'side' => $trade->side === 'buy' ? 'sell' : 'buy',
                    'type' => 'stop_loss',
                    'quantity' => $trade->quantity,
                    'price' => $currentPrice,
                    'total' => $currentPrice * (float) $trade->quantity,
                    'fee' => 0,
                    'status' => 'filled',
                    'executed_at' => now(),
                ]);

                $stopLoss->update(['status' => 'executed', 'executed_at' => now()]);
            }
        }
    }
}
