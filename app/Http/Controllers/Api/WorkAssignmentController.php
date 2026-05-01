<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkAssignment;
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
}
