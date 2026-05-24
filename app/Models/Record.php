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

    protected function filePath(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn (?string $value) => $value ? (
                str_starts_with($value, 'http') || str_starts_with($value, '/storage/')
                    ? $value
                    : '/storage/' . $value
            ) : null,
        );
    }

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
