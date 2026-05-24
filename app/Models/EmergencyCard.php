<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class EmergencyCard extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'emergency_cards';

    protected $fillable = [
        'patient_id',
        'is_public',
        'qr_token',
        'blood_group',
        'allergies',
        'critical_conditions',
        'medications',
        'emergency_contact',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'emergency_contact' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
