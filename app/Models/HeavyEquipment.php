<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeavyEquipment extends Model
{
    protected $table = 'heavy_equipments';

    protected $fillable = [
        'name',
        'nomor_lambung',
        'status',
        'merek',
        'tahun',
        'kondisi',
        'maintenance_schedule',
        'last_maintenance_date',
        'location',
        'current_location',
        'current_latitude',
        'current_longitude',
        'hours_meter',
    ];

    protected $casts = [
        'maintenance_schedule' => 'date',
        'last_maintenance_date' => 'date',
        'capacity' => 'decimal:2',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
    ];

    public function workAssignments()
    {
        return $this->hasMany(WorkAssignment::class);
    }

    public function integrations()
    {
        return $this->hasMany(HeavyEquipmentIntegration::class, 'heavy_equipment_id');
    }

    public function cartrackVehicles()
    {
        return $this->morphToMany(
            CartrackVehicle::class,
            'integratable',
            'heavy_equipment_integrations',
            'heavy_equipment_id',
            'integratable_id'
        );
    }
}
