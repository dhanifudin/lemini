<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageAnalytics extends Model
{
    protected $fillable = [
        'date',
        'learning_style',
        'visits',
        'conversions',
        'avg_time_on_page',
        'bounce_rate',
        'interaction_count',
    ];

    protected $casts = [
        'date' => 'date',
        'avg_time_on_page' => 'decimal:2',
        'bounce_rate' => 'decimal:2',
    ];
}
