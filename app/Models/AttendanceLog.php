<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_assignment_id',
        'user_id',
        'check_in_time',
        'check_out_time',
        'check_in_photo',
        'check_out_photo',
        'hours_meter_start',
        'hours_meter_end',
        'hours_meter_start_photo',
        'hours_meter_end_photo',
        'check_in_location',
        'check_out_location',
        'field_condition',
        'log_type', 
        'panjang_penanganan',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'hours_meter_start' => 'decimal:2',
        'hours_meter_end' => 'decimal:2',
    ];

    public function workAssignment()
    {
        return $this->belongsTo(WorkAssignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
