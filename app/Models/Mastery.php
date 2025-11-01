<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mastery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'objective_code',
        'level',
        'score',
        'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'score' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
