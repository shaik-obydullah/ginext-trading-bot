<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyPerformance extends Model
{
    use HasFactory;

    protected $table = 'daily_performance';

    protected $fillable = [
        'portfolio_id',
        'date',
        'start_value',
        'end_value',
        'pnl',
        'pnl_percentage',
    ];

    protected $casts = [
        'date' => 'date',
        'start_value' => 'decimal:8',
        'end_value' => 'decimal:8',
        'pnl' => 'decimal:8',
        'pnl_percentage' => 'decimal:4',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
