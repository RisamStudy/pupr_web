<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
/**
 * @property array|string $roles
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'roles',
        'types',
        'status',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'roles' => 'array',
        'types' => 'array',
    ];

    public function assignmentUsers()
    {
        return $this->hasMany(AssignmentUser::class);
    }

    public function workAssignments(): HasManyThrough
    {
        return $this->hasManyThrough(
            WorkAssignment::class,
            AssignmentUser::class,
            'user_id',
            'id',
            'id',
            'work_assignment_id'
        );
    }

    public function currentAssignment()
    {
        return $this->workAssignments()
            ->where('status', 'Sedang Berlangsung')
            ->with(['village', 'district', 'city'])
            ->first();
    }

    public function operatorAssignments()
    {
        return $this->workAssignments()->wherePivot('role', 'operator');
    }

    public function helperAssignments()
    {
        return $this->workAssignments()->wherePivot('role', 'helper');
    }
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isOperator()
    {
        return $this->hasRole('operator');
    }

    public function isHelper()
    {
        return $this->hasRole('helper');
    }
    public function hasRole($role)
    {
        if (is_array($this->roles)) {
            return in_array($role, $this->roles);
        }
        return $this->role === $role;
    }


    public function hasType($type)
    {
        return in_array($type, $this->types ?? []);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function calculateWorkHours($startDate = null, $endDate = null)
    {
        $query = $this->attendanceLogs()
            ->where('log_type','work')
            ->whereNotNull('check_in_time')
            ->whereNotNull('check_out_time');

        if ($startDate) {
            $query->where('check_in_time', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('check_out_time', '<=', $endDate);
        }
        $logs = $query->get();

        $totalHours = 0;
        $totalHoursMeter = 0;

        foreach ($logs as $log) {
            $checkIn = Carbon::parse($log->check_in_time);
            $checkOut = Carbon::parse($log->check_out_time);
            $duration = $checkIn->diffInHours($checkOut);

            $totalHours += $duration;

            if ($log->hours_meter_end && $log->hours_meter_start) {
                $totalHoursMeter += $log->hours_meter_end - $log->hours_meter_start;
            }
        }

        return [
            'total_hours' => round($totalHours, 2),
            'total_hours_meter' => round($totalHoursMeter, 2)
        ];
    }

}
