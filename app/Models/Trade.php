<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'strategy_id',
        'user_id',
        'exchange',
        'symbol',
        'side',
        'type',
        'quantity',
        'price',
        'total',
        'fee',
        'status',
        'executed_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:8',
        'price' => 'decimal:8',
        'total' => 'decimal:8',
        'fee' => 'decimal:8',
        'executed_at' => 'datetime',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function strategy(): BelongsTo
    {
        return $this->belongsTo(Strategy::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function stopLoss(): HasOne
    {
        return $this->hasOne(StopLossOrder::class);
    }
}
