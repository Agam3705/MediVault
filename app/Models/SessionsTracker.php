<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class SessionsTracker extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'sessions_tracker';

    protected $fillable = [
        'user_id',
        'session_token',
        'ip_address',
        'device',
        'logged_in_at',
        'last_active_at',
        'is_active',
    ];

    protected $casts = [
        'logged_in_at' => 'datetime',
        'last_active_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
