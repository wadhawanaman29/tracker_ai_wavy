<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAssignment extends Model
{
    use HasFactory;

    protected $table = 'project_assignment';

    protected $fillable = ['assigned_by', 'assigned_to', 'project', 'project_time', 'assignment_status', 'comment','project_date','created_at' ];
}
