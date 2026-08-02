<?php

namespace App\Services\Trading;

use App\Models\Portfolio;
use App\Models\PortfolioAsset;
use App\Models\Trade;

class PositionService
{
    public function applyTrade(Portfolio $portfolio, Trade $trade): void
    {
        $asset = $portfolio->assets()->firstOrCreate(
            ['symbol' => $trade->symbol],
            ['quantity' => 0, 'avg_buy_price' => 0]
        );

        $quantity = (float) $trade->quantity;
        $price = (float) ($trade->price ?? 0);

        if ($trade->side === 'buy') {
            $oldQuantity = (float) $asset->quantity;
            $oldCost = $oldQuantity * (float) $asset->avg_buy_price;
            $newQuantity = $oldQuantity + $quantity;
            $asset->avg_buy_price = $newQuantity > 0 ? ($oldCost + ($quantity * $price)) / $newQuantity : 0;
            $asset->quantity = $newQuantity;
        } else {
            $asset->quantity = max(0, (float) $asset->quantity - $quantity);
            if ((float) $asset->quantity == 0) {
                $asset->avg_buy_price = 0;
            }
        }

        $asset->save();
    }

    public function updateValues(Portfolio $portfolio, callable $priceResolver): void
    {
        foreach ($portfolio->assets as $asset) {
            if ((float) $asset->quantity <= 0) {
                continue;
            }
            $asset->current_value = (float) $asset->quantity * $priceResolver($asset->symbol);
            $asset->save();
        }
    }

    public function totalValue(Portfolio $portfolio, callable $priceResolver): float
    {
        $total = 0.0;
        foreach ($portfolio->assets as $asset) {
            if ((float) $asset->quantity <= 0) {
                continue;
            }
            $total += (float) $asset->quantity * $priceResolver($asset->symbol);
        }

        return $total;
    }
}
