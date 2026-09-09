<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'reason',
        'start_date',
        'end_date',
        'leave_time',
        'status',
        'total_leave_days',
        'leave_type',
        'from_time',
        'to_time',
        'action_reason',
        // Add other fillable fields here if any
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
