<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;

class WorkAssignment extends Model
{
    protected $fillable = [
        'heavy_equipment_id',
        'alamat',
        'latitude',
        'longitude',
        'start_date',
        'end_date',
        'project_name',
        'expected_duration',
        'tipe_pekerjaan',
        'permasalahan',
        'city_id',
        'district_id',
        'village_id',
        'panjang_penanganan',
        'completion_date',
        'status',
        'start_hours_meter',
        'end_hours_meter',
        'start_hours_meter_image',
        'end_hours_meter_image',
        'documentation_link',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'completion_date' => 'datetime',

    ];

    public static function completeExpiredAssignments(): int
    {
        $today = Carbon::today();

        return static::query()
            ->whereDate('end_date', '<', $today)
            ->where('status', '!=', 'Selesai')
            ->update([
                'status' => 'Selesai',
                'completion_date' => DB::raw('COALESCE(completion_date, end_date)'),
            ]);
    }

    public function heavyEquipment()
    {
        return $this->belongsTo(HeavyEquipment::class);
    }

    public function assignmentUsers()
    {
        return $this->hasMany(AssignmentUser::class);
    }

    public function operators()
    {
        return $this->assignmentUsers()->whereHas('user', function ($query) {
            $query->whereJsonContains('roles', 'operator');
        })->with('user');
    }

    public function helpers()
    {
        return $this->assignmentUsers()->whereHas('user', function ($query) {
            $query->whereJsonContains('roles', 'helper');
        })->with('user');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'code');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'code');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id', 'code');
    }
     // Tambahkan relasi attendanceLogs
     public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function fieldConditionPhotos()
    {
        return $this->hasMany(FieldConditionPhoto::class);
    }

}
