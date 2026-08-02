<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rule_type',
        'parameters_json',
        'status',
    ];

    protected $casts = [
        'parameters_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
