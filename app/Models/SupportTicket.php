<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class SupportTicket extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'support_tickets';

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'reply',
        'status', // pending, resolved
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
