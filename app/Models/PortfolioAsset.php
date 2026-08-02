<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'symbol',
        'quantity',
        'avg_buy_price',
        'current_value',
    ];

    protected $casts = [
        'quantity' => 'decimal:8',
        'avg_buy_price' => 'decimal:8',
        'current_value' => 'decimal:8',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
