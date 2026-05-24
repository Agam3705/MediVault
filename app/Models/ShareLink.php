<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ShareLink extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'share_links';

    protected $fillable = [
        'user_id',
        'record_id',
        'token',
        'password', // Nullable or hashed
        'expires_at',
        'access_count',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'access_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function record()
    {
        return $this->belongsTo(Record::class);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
