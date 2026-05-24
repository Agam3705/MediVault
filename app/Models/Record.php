<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Record extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'records';

    protected $fillable = [
        'patient_id',
        'created_by',
        'type',
        'title',
        'description',
        'file_path',
        'file_type',
        'is_critical',
    ];

    protected $casts = [
        'is_critical' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions()
    {
        return $this->hasMany(RecordVersion::class);
    }
}
