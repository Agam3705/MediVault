<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class UserSetting extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'user_settings';

    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
