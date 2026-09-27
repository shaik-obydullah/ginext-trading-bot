<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StopLossOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'trade_id',
        'trigger_price',
        'status',
        'executed_at',
    ];

    protected $casts = [
        'trigger_price' => 'decimal:8',
        'executed_at' => 'datetime',
    ];

    public function trade(): BelongsTo
    {
        return $this->belongsTo(Trade::class);
    }
}
