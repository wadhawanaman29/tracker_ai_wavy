<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'user_id',
        'office_in_time',
        'office_out_time',
        'lunch_in_time',
        'lunch_out_time',
        'break_in_time',
        'break_out_time',
        'total_working_hours',
        'location',
        'ip',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function trackerLogs(): HasMany
    {
        return $this->hasMany(TrackerLog::class, 'attendance_id');
    }
}
