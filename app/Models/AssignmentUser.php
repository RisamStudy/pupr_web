<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignmentUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'work_assignment_id',
        'user_id',
        'role',
        'start_date',
        'end_date'
    ];

    protected $dates = ['deleted_at'];

    public function workAssignment()
    {
        return $this->belongsTo(WorkAssignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
