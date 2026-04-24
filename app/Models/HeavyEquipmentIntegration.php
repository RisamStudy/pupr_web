<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeavyEquipmentIntegration extends Model
{
    //
    protected $table = 'heavy_equipment_integrations';

    protected $fillable = [
        'heavy_equipment_id',
        'integratable_id',
        'integratable_type',
    ];

    public function heavyEquipment()
    {
        return $this->belongsTo(HeavyEquipment::class, 'heavy_equipment_id');
    }

    public function integratable()
    {
        return $this->morphTo();
    }
}
