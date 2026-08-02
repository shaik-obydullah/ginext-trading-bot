<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'trade_id',
        'exchange_order_id',
        'type',
        'status',
        'filled_quantity',
        'remaining_quantity',
    ];

    protected $casts = [
        'filled_quantity' => 'decimal:8',
        'remaining_quantity' => 'decimal:8',
    ];

    public function trade(): BelongsTo
    {
        return $this->belongsTo(Trade::class);
    }
}
