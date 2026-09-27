<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeHistory extends Model
{
    use HasFactory;

    protected $table = 'trade_history';

    protected $fillable = [
        'portfolio_id',
        'period_start',
        'period_end',
        'total_trades',
        'winning_trades',
        'losing_trades',
        'total_pnl',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'total_pnl' => 'decimal:8',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
