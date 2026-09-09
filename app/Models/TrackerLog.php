<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackerLog extends Model
{
    use HasFactory;

    protected $table = 'tracker_users_logs';

    protected $fillable = [
        'user_id',
        'attendance_id',
        'start_time',
        'stop_time',
        'duration',
        'session_count',
        'active',
        'inactive',
        'ip',
        'remarks'
    ];


    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
