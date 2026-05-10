<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkAssignment;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkAssignmentController extends Controller
{
    public function ongoing(Request $request): JsonResponse
    {
        $query = WorkAssignment::with([
            'heavyEquipment',
            'assignmentUsers.user',
            'city',
            'district',
            'village',
            'fieldConditionPhotos' => fn ($query) => $query->latest()->limit(1),
        ])
            ->where('status', 'Sedang Berlangsung')
            ->latest();

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->integer('year'));
        }

        return response()->json([
            'status' => true,
            'message' => 'Daftar pekerjaan yang sedang berlangsung',
            'data' => $query->get()->map(fn (WorkAssignment $assignment) => [
                'id' => $assignment->id,
                'project_name' => $assignment->project_name,
                'status' => $assignment->status,
                'tipe_pekerjaan' => $assignment->tipe_pekerjaan,
                'permasalahan' => $assignment->permasalahan,
                'alamat' => $assignment->alamat,
                'latitude' => $assignment->latitude,
                'longitude' => $assignment->longitude,
                'start_date' => optional($assignment->start_date)->toDateString(),
                'end_date' => optional($assignment->end_date)->toDateString(),
                'expected_duration' => $assignment->expected_duration,
                'panjang_penanganan' => $assignment->panjang_penanganan,
                'documentation_link' => $assignment->documentation_link,
                'heavy_equipment' => $assignment->heavyEquipment ? [
                    'id' => $assignment->heavyEquipment->id,
                    'name' => $assignment->heavyEquipment->name,
                    'nomor_lambung' => $assignment->heavyEquipment->nomor_lambung,
                    'status' => $assignment->heavyEquipment->status,
                ] : null,
                'location' => [
                    'city' => $assignment->city?->name,
                    'district' => $assignment->district?->name,
                    'village' => $assignment->village?->name,
                ],
                'operators' => $assignment->assignmentUsers
                    ->where('role', 'operator')
                    ->values()
                    ->map(fn ($assignmentUser) => [
                        'id' => $assignmentUser->user?->id,
                        'name' => $assignmentUser->user?->name,
                    ]),
                'helpers' => $assignment->assignmentUsers
                    ->where('role', 'helper')
                    ->values()
                    ->map(fn ($assignmentUser) => [
                        'id' => $assignmentUser->user?->id,
                        'name' => $assignmentUser->user?->name,
                    ]),
                'latest_photo_url' => $assignment->fieldConditionPhotos->first()
                    ? asset($assignment->fieldConditionPhotos->first()->photo_path)
                    : null,
            ]),
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = min($request->integer('per_page', 10), 50);

        $query = WorkAssignment::with([
            'heavyEquipment',
            'assignmentUsers.user',
            'city',
            'district',
            'village',
            'attendanceLogs' => fn ($query) => $query
                ->where('user_id', $user->id)
                ->latest(),
            'fieldConditionPhotos' => fn ($query) => $query
                ->where('uploaded_by', $user->id)
                ->latest(),
        ])
            ->whereHas('assignmentUsers', fn ($query) => $query->where('user_id', $user->id))
            ->latest('start_date');

        if ($request->filled('search')) {
            $query->where('project_name', 'like', '%' . $request->string('search') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', Carbon::parse($request->date('date_from')));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', Carbon::parse($request->date('date_to')));
        }

        $assignments = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Riwayat pekerjaan user',
            'data' => $assignments->getCollection()->map(fn (WorkAssignment $assignment) => $this->formatAssignment($assignment)),
            'meta' => [
                'current_page' => $assignments->currentPage(),
                'last_page' => $assignments->lastPage(),
                'per_page' => $assignments->perPage(),
                'total' => $assignments->total(),
            ],
        ]);
    }

    public function historyDetail(Request $request, WorkAssignment $workAssignment): JsonResponse
    {
        $user = $request->user();

        $isAssigned = $workAssignment->assignmentUsers()
            ->where('user_id', $user->id)
            ->exists();

        if (! $isAssigned) {
            return response()->json([
                'status' => false,
                'message' => 'Riwayat pekerjaan tidak ditemukan untuk user ini',
            ], 404);
        }

        $workAssignment->load([
            'heavyEquipment',
            'assignmentUsers.user',
            'city',
            'district',
            'village',
            'attendanceLogs' => fn ($query) => $query
                ->where('user_id', $user->id)
                ->latest(),
            'fieldConditionPhotos' => fn ($query) => $query
                ->where('uploaded_by', $user->id)
                ->latest(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Detail riwayat pekerjaan',
            'data' => $this->formatAssignment($workAssignment, true),
        ]);
    }

    private function formatAssignment(WorkAssignment $assignment, bool $withDetail = false): array
    {
        $data = [
            'id' => $assignment->id,
            'project_name' => $assignment->project_name,
            'status' => $assignment->status,
            'tipe_pekerjaan' => $assignment->tipe_pekerjaan,
            'permasalahan' => $assignment->permasalahan,
            'alamat' => $assignment->alamat,
            'latitude' => $assignment->latitude,
            'longitude' => $assignment->longitude,
            'start_date' => $this->dateToString($assignment->start_date),
            'end_date' => $this->dateToString($assignment->end_date),
            'completion_date' => $this->dateToString($assignment->completion_date),
            'expected_duration' => $assignment->expected_duration,
            'panjang_penanganan' => $assignment->panjang_penanganan,
            'documentation_link' => $assignment->documentation_link,
            'heavy_equipment' => $assignment->heavyEquipment ? [
                'id' => $assignment->heavyEquipment->id,
                'name' => $assignment->heavyEquipment->name,
                'nomor_lambung' => $assignment->heavyEquipment->nomor_lambung,
                'status' => $assignment->heavyEquipment->status,
            ] : null,
            'location' => [
                'city' => $assignment->city?->name,
                'district' => $assignment->district?->name,
                'village' => $assignment->village?->name,
            ],
            'operators' => $assignment->assignmentUsers
                ->where('role', 'operator')
                ->values()
                ->map(fn ($assignmentUser) => [
                    'id' => $assignmentUser->user?->id,
                    'name' => $assignmentUser->user?->name,
                ]),
            'helpers' => $assignment->assignmentUsers
                ->where('role', 'helper')
                ->values()
                ->map(fn ($assignmentUser) => [
                    'id' => $assignmentUser->user?->id,
                    'name' => $assignmentUser->user?->name,
                ]),
            'latest_photo_url' => $assignment->fieldConditionPhotos->first()
                ? asset($assignment->fieldConditionPhotos->first()->photo_path)
                : null,
            'attendance_summary' => [
                'total_logs' => $assignment->attendanceLogs->count(),
                'last_check_in_time' => optional($assignment->attendanceLogs->first()?->check_in_time)->toDateTimeString(),
                'last_check_out_time' => optional($assignment->attendanceLogs->first()?->check_out_time)->toDateTimeString(),
            ],
        ];

        if ($withDetail) {
            $data['attendance_logs'] = $assignment->attendanceLogs
                ->values()
                ->map(fn ($log) => [
                    'id' => $log->id,
                    'log_type' => $log->log_type,
                    'check_in_time' => optional($log->check_in_time)->toDateTimeString(),
                    'check_out_time' => optional($log->check_out_time)->toDateTimeString(),
                    'check_in_photo_url' => $log->check_in_photo ? asset($log->check_in_photo) : null,
                    'check_out_photo_url' => $log->check_out_photo ? asset($log->check_out_photo) : null,
                    'hours_meter_start' => $log->hours_meter_start,
                    'hours_meter_end' => $log->hours_meter_end,
                    'hours_meter_start_photo_url' => $log->hours_meter_start_photo ? asset($log->hours_meter_start_photo) : null,
                    'hours_meter_end_photo_url' => $log->hours_meter_end_photo ? asset($log->hours_meter_end_photo) : null,
                    'check_in_location' => $log->check_in_location,
                    'check_out_location' => $log->check_out_location,
                    'field_condition' => $log->field_condition,
                    'panjang_penanganan' => $log->panjang_penanganan,
                ]);

            $data['field_condition_photos'] = $assignment->fieldConditionPhotos
                ->values()
                ->map(fn ($photo) => [
                    'id' => $photo->id,
                    'photo_url' => asset($photo->photo_path),
                    'latitude' => $photo->latitude,
                    'longitude' => $photo->longitude,
                    'is_treatment_point' => $photo->is_treatment_point,
                    'order' => $photo->order,
                    'created_at' => optional($photo->created_at)->toDateTimeString(),
                ]);
        }

        return $data;
    }

    private function dateToString($date): ?string
    {
        if (! $date) {
            return null;
        }

        if ($date instanceof CarbonInterface) {
            return $date->toDateString();
        }

        return Carbon::parse($date)->toDateString();
    }
}
