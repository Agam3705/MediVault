<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Doctor extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'doctors';

    protected $fillable = [
        'user_id',
        'license_number',
        'specialization',
        'hospital',
        'bio',
        'verified_at',
        'verified_by',
        'phone',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accessGrants()
    {
        return $this->hasMany(AccessGrant::class);
    }

    public function accessRequests()
    {
        return $this->hasMany(AccessRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if the doctor is verified by admin.
     */
    public function isVerified()
    {
        return !is_null($this->verified_at);
    }
}
