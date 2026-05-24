<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Patient extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'patients';

    protected $fillable = [
        'user_id',
        'dob',
        'gender',
        'blood_group',
        'phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'address',
        'profile_photo',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function accessGrants()
    {
        return $this->hasMany(AccessGrant::class);
    }

    public function accessRequests()
    {
        return $this->hasMany(AccessRequest::class);
    }

    public function emergencyCard()
    {
        return $this->hasOne(EmergencyCard::class);
    }

    /**
     * Compute profile completeness percentage.
     */
    public function getCompletenessScoreAttribute()
    {
        $fields = [
            'dob',
            'gender',
            'blood_group',
            'phone',
            'emergency_contact_name',
            'emergency_contact_phone',
            'address',
            'profile_photo'
        ];
        
        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filled++;
            }
        }
        
        return round(($filled / count($fields)) * 100);
    }
}
