<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QuizSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'experiment_variant',
        'status',
        'settings',
        'started_at',
        'submitted_at',
        'average_score',
        'correct_count',
        'incorrect_count',
        'pending_review_count',
    ];

    protected $casts = [
        'settings' => 'array',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'average_score' => 'float',
        'correct_count' => 'integer',
        'incorrect_count' => 'integer',
        'pending_review_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuizSessionItem::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(QuizReview::class);
    }
}
