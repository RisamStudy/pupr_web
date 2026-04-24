<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartrackVehicleStatus extends Model
{
    //
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cartrack_vehicle_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cartrack_vehicle_id',
        'event_ts',
        'vext',
        'fuel_level',
        'ignition',
    ];

    /**
     * Get the user that owns the CartrackVehicleActivity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cartrackVehicle(): BelongsTo
    {
        return $this->belongsTo(CartrackVehicle::class, 'cartrack_vehicle_id', 'vehicle_id');
    }
}
