<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;

class AttendanceController extends Controller
{
    public function today(Request $request): JsonResponse
    {
        $attendanceLog = AttendanceLog::where('user_id', $request->user()->id)
            ->where('log_type', 'attendance')
            ->whereDate('check_in_time', now()->toDateString())
            ->latest()
            ->first();

        return response()->json([
            'status' => true,
            'message' => $attendanceLog ? 'Data absen hari ini ditemukan' : 'Belum ada absen hari ini',
            'data' => $attendanceLog ? $this->formatAttendanceLog($attendanceLog) : null,
        ]);
    }

    public function checkIn(Request $request): JsonResponse
    {
        $user = $request->user();
        $now = now();

        if ($now->hour >= 22) {
            return response()->json([
                'status' => false,
                'message' => 'Maaf, waktu absen masuk sudah lewat.',
            ], 422);
        }

        $existingCheckIn = AttendanceLog::where('user_id', $user->id)
            ->where('log_type', 'attendance')
            ->whereDate('check_in_time', $now->toDateString())
            ->exists();

        if ($existingCheckIn) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah melakukan absen masuk hari ini. Absen masuk hanya dapat dilakukan sekali per hari.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'location' => 'required|string',
            'work_assignment_id' => 'required|exists:work_assignments,id',
            'check_in_photo' => 'required|image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $imagePath = $this->saveCompressedImage($request->file('check_in_photo'), 'check_in_photos');

            $attendanceLog = AttendanceLog::create([
                'user_id' => $user->id,
                'work_assignment_id' => $request->input('work_assignment_id'),
                'log_type' => 'attendance',
                'check_in_time' => $now,
                'check_in_location' => $request->input('location'),
                'check_in_photo' => $imagePath,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Absen masuk berhasil dicatat.',
                'data' => $this->formatAttendanceLog($attendanceLog),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('API daily check-in failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat melakukan absen masuk.',
            ], 500);
        }
    }

    public function checkOut(Request $request): JsonResponse
    {
        $user = $request->user();
        $now = now();

        $attendanceLog = AttendanceLog::where('user_id', $user->id)
            ->where('log_type', 'attendance')
            ->whereDate('check_in_time', $now->toDateString())
            ->whereNull('check_out_time')
            ->latest()
            ->first();

        if (! $attendanceLog) {
            return response()->json([
                'status' => false,
                'message' => 'Anda belum melakukan absen masuk hari ini atau sudah melakukan absen keluar.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'location' => 'required|string',
            'work_assignment_id' => 'required|exists:work_assignments,id',
            'check_out_photo' => 'required|image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $imagePath = $this->saveCompressedImage($request->file('check_out_photo'), 'check_out_photos');

            $attendanceLog->update([
                'check_out_time' => $now,
                'check_out_location' => $request->input('location'),
                'check_out_photo' => $imagePath,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Absen keluar berhasil dicatat.',
                'data' => $this->formatAttendanceLog($attendanceLog->fresh()),
            ]);
        } catch (\Throwable $e) {
            Log::error('API daily check-out failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat melakukan absen keluar.',
            ], 500);
        }
    }

    private function saveCompressedImage($file, string $directory): string
    {
        $filename = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
        $path = config('filesystems.uploads_base_path') . '/' . $directory;

        if (! file_exists($path) && ! mkdir($path, 0777, true)) {
            throw new \RuntimeException("Failed to create directory: $path");
        }

        $manager = new ImageManager(new GdDriver());
        $img = $manager->read($file);
        $targetWidth = 800;
        $targetHeight = intval($img->height() * ($targetWidth / $img->width()));

        $img->resize($targetWidth, $targetHeight);
        $img->toWebp(80)->save($path . '/' . $filename);

        if (! file_exists("$path/$filename")) {
            throw new \RuntimeException("File was not found after saving: $path/$filename");
        }

        return "/uploads/$directory/$filename";
    }

    private function formatAttendanceLog(AttendanceLog $attendanceLog): array
    {
        return [
            'id' => $attendanceLog->id,
            'user_id' => $attendanceLog->user_id,
            'work_assignment_id' => $attendanceLog->work_assignment_id,
            'log_type' => $attendanceLog->log_type,
            'check_in_time' => optional($attendanceLog->check_in_time)->toDateTimeString(),
            'check_out_time' => optional($attendanceLog->check_out_time)->toDateTimeString(),
            'check_in_location' => $attendanceLog->check_in_location,
            'check_out_location' => $attendanceLog->check_out_location,
            'check_in_photo_url' => $attendanceLog->check_in_photo ? asset($attendanceLog->check_in_photo) : null,
            'check_out_photo_url' => $attendanceLog->check_out_photo ? asset($attendanceLog->check_out_photo) : null,
        ];
    }
}
