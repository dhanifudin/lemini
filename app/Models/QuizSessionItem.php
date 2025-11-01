<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizSessionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_session_id',
        'item_id',
        'position',
        'response',
        'score',
        'status',
        'feedback',
        'flagged',
    ];

    protected $casts = [
        'response' => 'array',
        'score' => 'float',
        'feedback' => 'array',
        'flagged' => 'boolean',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(QuizSession::class, 'quiz_session_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
