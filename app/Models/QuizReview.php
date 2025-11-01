<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_session_id',
        'teacher_id',
        'status',
        'notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(QuizSession::class, 'quiz_session_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
