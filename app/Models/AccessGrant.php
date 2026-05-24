<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AccessGrant extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'access_grants';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'granted_at',
        'expires_at',
        'is_active',
        'access_type',
        'revoked_at',
        'revoked_reason',
        'restricted_record_ids',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'is_active' => 'boolean',
        'restricted_record_ids' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Check if the grant is currently valid (active and not expired).
     */
    public function isValid()
    {
        return $this->is_active 
            && is_null($this->revoked_at) 
            && ($this->expires_at ? $this->expires_at->isFuture() : true);
    }

    /**
     * Check if a specific record is restricted (hidden) from this doctor.
     */
    public function isRecordRestricted($recordId)
    {
        if (empty($this->restricted_record_ids)) {
            return false;
        }
        return in_array((string)$recordId, $this->restricted_record_ids);
    }
}
