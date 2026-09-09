<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyLeaves extends Model
{
    // use HasFactory;

    protected $fillable = [

    'name',
    'leave_type',
    // 'start_date',
    // 'end_date',
    'leave_date',
    'start_time',
    'end_time'
    ];
}
