<?php

namespace App\Services\Trading;

use App\Models\Order;
use App\Models\Portfolio;
use App\Models\Trade;
use App\Models\User;
use App\Services\Exchange\BinanceService;

class OrderService
{
    public function __construct(
        protected BinanceService $binance,
    ) {
    }

    public function create(array $data): Trade
    {
        $trade = Trade::create([
            'portfolio_id' => $data['portfolio_id'] ?? null,
            'strategy_id' => $data['strategy_id'] ?? null,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'exchange' => $data['exchange'] ?? 'binance',
            'symbol' => $data['symbol'],
            'side' => $data['side'],
            'type' => $data['type'] ?? 'market',
            'quantity' => $data['quantity'],
            'price' => $data['price'] ?? null,
            'total' => $data['total'] ?? null,
            'fee' => $data['fee'] ?? 0,
            'status' => 'pending',
        ]);

        Order::create([
            'trade_id' => $trade->id,
            'type' => $trade->type,
            'status' => 'pending',
            'filled_quantity' => 0,
            'remaining_quantity' => $trade->quantity,
        ]);

        return $trade;
    }

    public function execute(Trade $trade, array $fillPrice = null): Trade
    {
        $price = $fillPrice ?? $this->binance->getTicker($trade->symbol)['price'];

        $trade->price = $price;
        $trade->total = $price * (float) $trade->quantity;
        $trade->fee = $trade->total * 0.001;
        $trade->status = 'filled';
        $trade->executed_at = now();
        $trade->save();

        $order = $trade->orders()->first();
        if ($order) {
            $order->update([
                'status' => 'filled',
                'filled_quantity' => $trade->quantity,
                'remaining_quantity' => 0,
            ]);
        }

        if ($trade->portfolio_id) {
            app(PositionService::class)->applyTrade($trade->portfolio, $trade);
        }

        return $trade;
    }

    public function cancel(Trade $trade): Trade
    {
        $trade->status = 'cancelled';
        $trade->save();

        $trade->orders()->update(['status' => 'cancelled']);

        return $trade;
    }
}
