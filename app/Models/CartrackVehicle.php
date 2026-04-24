<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartrackVehicle extends Model
{
    //
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cartrack_vehicles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'terminal_id',
        'terminal_serial',
        'registration',
        'vehicle_name',
        'manufacturer',
        'model',
        'model_year',
        'colour',
        'chassis_number',
    ];

    /**
     * Get the cartrackVehicleActivity associated with the CartrackVehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartrackVehicleActivity(): HasMany
    {
        return $this->hasMany(CartrackVehicleActivity::class, 'cartrack_vehicle_id', 'vehicle_id');
    }

    /**
     * Get the cartrackVehicleActivity associated with the CartrackVehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartrackVehicleStatuses(): HasMany
    {
        return $this->hasMany(CartrackVehicleStatus::class, 'cartrack_vehicle_id', 'vehicle_id');
    }

    /**
     * Get the latestActivity associated with the CartrackVehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestActivity(): HasOne
    {
        return $this->hasOne(CartrackVehicleActivity::class, 'cartrack_vehicle_id', 'vehicle_id')
            ->latestOfMany();
    }

    public function heavyEquipment()
    {
        return $this->morphedByMany(
            HeavyEquipment::class,
            'integratable',
            'heavy_equipment_integrations',
            'integratable_id',
            'heavy_equipment_id'
        );
    }
}
