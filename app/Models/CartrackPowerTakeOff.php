<?php

namespace App\Models;

use App\Models\CartrackVehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartrackPowerTakeOff extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cartrack_power_take_offs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cartrack_vehicle_id',
        'event_time',
        'status',
    ];

    /**
     * Get the user that owns the CartrackVehicleActivity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cartrack_vehicle(): BelongsTo
    {
        return $this->belongsTo(CartrackVehicle::class, 'cartrack_vehicle_id', 'vehicle_id');
    }
}
