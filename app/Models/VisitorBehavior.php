<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorBehavior extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'event_type',
        'event_data',
        'event_timestamp',
    ];

    protected $casts = [
        'event_data' => 'array',
        'event_timestamp' => 'datetime',
    ];
}
