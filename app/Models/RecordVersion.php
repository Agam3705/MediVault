<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class RecordVersion extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'record_versions';

    protected $fillable = [
        'record_id',
        'changed_by',
        'snapshot_json',
        'change_note',
    ];

    protected $casts = [
        'snapshot_json' => 'array',
    ];

    public function record()
    {
        return $this->belongsTo(Record::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
