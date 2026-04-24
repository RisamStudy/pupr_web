<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletedProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'completion_date',
        'description',
    ];

    protected $casts = [
        'completion_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];
}
