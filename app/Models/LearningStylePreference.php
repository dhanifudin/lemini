<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningStylePreference extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'learning_style',
        'detection_method',
        'confidence_score',
        'quiz_results',
    ];

    protected $casts = [
        'quiz_results' => 'array',
        'confidence_score' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
