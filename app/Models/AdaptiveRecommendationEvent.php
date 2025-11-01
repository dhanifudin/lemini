<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdaptiveRecommendationEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'experiment_key',
        'variant',
        'event_type',
        'score_before',
        'score_after',
        'meta',
        'occurred_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'occurred_at' => 'datetime',
        'score_before' => 'float',
        'score_after' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
