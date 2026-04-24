<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldConditionPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_assignment_id',
        'photo_path',
        'latitude',
        'longitude',
        'is_treatment_point',
        'order',
        'uploaded_by'
    ];

    protected $casts = [
        'is_treatment_point' => 'boolean',
    ];

    public function workAssignment()
    {
        return $this->belongsTo(WorkAssignment::class);
    }
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
