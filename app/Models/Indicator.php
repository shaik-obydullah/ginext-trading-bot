<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'indicator_type',
        'value',
        'period',
        'calculated_at',
    ];

    protected $casts = [
        'value' => 'decimal:8',
        'calculated_at' => 'datetime',
    ];
}
