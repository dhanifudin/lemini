<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\FeedbackAudit;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'ai_text',
        'human_revision',
        'status',
        'released_at',
    ];

    protected $casts = [
        'ai_text' => 'array',
        'released_at' => 'datetime',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class);
    }

    public function reflections(): HasMany
    {
        return $this->hasMany(FeedbackReflection::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(FeedbackAudit::class);
    }
}
