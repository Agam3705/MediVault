<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AccessRequest extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'access_requests';

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'reason',
        'status',
        'expires_at',
        'responded_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Check if request is currently pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request has expired.
     */
    public function isExpired()
    {
        return $this->status === 'expired' || ($this->expires_at && $this->expires_at->isPast());
    }
}
