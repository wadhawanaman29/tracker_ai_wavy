<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $table = 'attendance_policy';

    protected $fillable = [
        'late_allow',
        'late_deduction',

        'short_leave_allow',
        'short_leave_deduction',

        'half_day_allow',
        'half_day_deduction',

        'absent_allow',
        'absent_deduction',

        'sandwich_deduction',
    ];
}