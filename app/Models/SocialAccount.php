<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class SocialAccount extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'social_accounts';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_user_id',
        'avatar_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
