<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'rubric_id',
        'learning_objective_id',
        'objective_code',
        'stem',
        'type',
        'options',
        'answer',
        'rationale',
        'meta',
        'is_quiz_eligible',
    ];

    protected $casts = [
        'options' => 'array',
        'meta' => 'array',
        'is_quiz_eligible' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Item $item): void {
            if ($item->learning_objective_id) {
                $objective = $item->learningObjective()->withoutGlobalScopes()->first();

                if ($objective && $item->objective_code !== $objective->code) {
                    $item->objective_code = $objective->code;
                }
            }
        });
    }

    public function rubric(): BelongsTo
    {
        return $this->belongsTo(Rubric::class);
    }

    public function learningObjective(): BelongsTo
    {
        return $this->belongsTo(LearningObjective::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }
}
