<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdaptiveExperimentAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'experiment_key',
        'variant',
        'meta',
        'assigned_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'assigned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
