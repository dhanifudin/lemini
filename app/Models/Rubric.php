<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rubric extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'criteria',
        'levels',
    ];

    protected $casts = [
        'criteria' => 'array',
        'levels' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
