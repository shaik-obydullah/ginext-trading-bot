<?php

namespace App\Services\Analytics;

use App\Models\DailyPerformance;
use App\Models\Portfolio;
use App\Models\Trade;

class PerformanceService
{
    public function snapshotDaily(Portfolio $portfolio, callable $valueResolver): void
    {
        $date = now()->toDateString();
        $previous = DailyPerformance::where('portfolio_id', $portfolio->id)
            ->where('date', '<', $date)
            ->orderByDesc('date')
            ->first();

        $startValue = $previous->end_value ?? 0;
        $endValue = $valueResolver($portfolio);

        $pnl = $endValue - $startValue;
        $pnlPercentage = $startValue > 0 ? ($pnl / $startValue) * 100 : 0;

        DailyPerformance::updateOrCreate(
            [
                'portfolio_id' => $portfolio->id,
                'date' => $date,
            ],
            [
                'start_value' => $startValue,
                'end_value' => $endValue,
                'pnl' => $pnl,
                'pnl_percentage' => $pnlPercentage,
            ]
        );
    }

    public function winRate(int $userId): array
    {
        $trades = Trade::where('user_id', $userId)
            ->where('status', 'filled')
            ->get();

        $wins = 0;
        $losses = 0;
        $totalPnl = 0.0;

        $bySymbol = [];
        foreach ($trades as $trade) {
            $symbol = $trade->symbol;
            $bySymbol[$symbol][] = $trade;
        }

        foreach ($bySymbol as $symbol => $symbolTrades) {
            $cost = 0.0;
            $quantity = 0.0;
            $proceeds = 0.0;
            $sold = 0.0;

            foreach ($symbolTrades as $trade) {
                if ($trade->side === 'buy') {
                    $quantity += (float) $trade->quantity;
                    $cost += (float) $trade->total;
                } else {
                    $sold += (float) $trade->quantity;
                    $proceeds += (float) $trade->total;
                }
            }

            if ($sold > 0 && $quantity > 0) {
                $pnl = $proceeds - ($cost * ($sold / $quantity));
                $totalPnl += $pnl;
                if ($pnl > 0) {
                    $wins++;
                } else {
                    $losses++;
                }
            }
        }

        $total = $wins + $losses;

        return [
            'total_trades' => $trades->count(),
            'winning_trades' => $wins,
            'losing_trades' => $losses,
            'win_rate' => $total > 0 ? ($wins / $total) * 100 : 0,
            'total_pnl' => $totalPnl,
        ];
    }
}
