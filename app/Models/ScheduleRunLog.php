<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleRunLog extends Model
{
    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'command',
        'expression',
        'status',
        'message',
        'exception',
        'started_at',
        'finished_at',
        'duration_ms',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }
}
