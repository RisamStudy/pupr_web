<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\WorkAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class AttendanceController extends Controller
{
    private function saveCompressedImage($file, $directory)
    {
        try {
            $filename = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $path = config('filesystems.uploads_base_path') . '/' . $directory;

            if (!file_exists($path)) {
                if (!mkdir($path, 0777, true)) {
                    throw new \Exception("Failed to create directory: $path");
                }
            }

            // Create an instance of ImageManager
            $manager = new ImageManager(new GdDriver());

            // Read the image
            $img = $manager->read($file);

            // Calculate new dimensions while maintaining aspect ratio
            $width = $img->width();
            $height = $img->height();
            $targetWidth = 800; // You can adjust this value
            $targetHeight = intval($height * ($targetWidth / $width));

            // Resize the image
            $img->resize($targetWidth, $targetHeight);

            // Convert and save as WebP
            $img->toWebp(80)->save($path . '/' . $filename);

            // Verify the file was actually saved
            if (!file_exists("$path/$filename")) {
                throw new \Exception("File was not found after saving: $path/$filename");
            }

            Log::info("File successfully saved and compressed: $path/$filename");

            // Return the path relative to the public directory
            return "/uploads/$directory/$filename";
        } catch (\Exception $e) {
            Log::error('Error saving compressed image: ' . $e->getMessage());
            Log::error('File details: ' . json_encode($file->getClientOriginalName()));
            Log::error('Target path: ' . $path);
            throw $e;
        }
    }
    public function dailyCheckIn(Request $request)
    {
        try {
            $user = Auth::user();
            $now = now();
            $errors = [];

            if ($now->hour >= 22) {
                $errors[] = 'Maaf, waktu absen masuk sudah lewat.';
            }

            $existingCheckIn = AttendanceLog::where('user_id', $user->id)
                ->where('log_type', 'attendance')
                ->whereDate('check_in_time', $now->toDateString())
                ->exists();

            if ($existingCheckIn) {
                $errors[] = 'Anda sudah melakukan absen masuk hari ini. Absen masuk hanya dapat dilakukan sekali per hari.';
            }

            if (!empty($errors)) {
                return redirect()->back()->with('error', implode(' ', $errors));
            }

            $validated = $request->validate([
                'location' => 'required|string',
                'work_assignment_id' => 'required|exists:work_assignments,id',
                'check_in_photo' => 'required|image', // max 2MB
            ], [
                'location.required' => 'Lokasi absen tidak terdeteksi. Pastikan GPS Anda aktif.',
                'work_assignment_id.required' => 'ID penugasan tidak ditemukan. Silakan hubungi admin.',
                'work_assignment_id.exists' => 'ID penugasan tidak valid. Silakan hubungi admin.',
                'check_in_photo.required' => 'Foto absen masuk wajib diunggah.',
                'check_in_photo.image' => 'File yang diunggah harus berupa gambar.',
            ]);

            $imagePath = $this->saveCompressedImage($request->file('check_in_photo'), 'check_in_photos');

            $attendanceLog = new AttendanceLog([
                'user_id' => $user->id,
                'work_assignment_id' => $validated['work_assignment_id'],
                'log_type' => 'attendance',
                'check_in_time' => $now,
                'check_in_location' => $validated['location'],
                'check_in_photo' => $imagePath,
            ]);

            $attendanceLog->save();

            return redirect()->route('operator-helper.dashboard')->with('success', 'Absen masuk berhasil dicatat pada ' . $now->format('H:i:s') . '. Selamat bekerja!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Daily check-in validation failed: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Daily check-in failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat melakukan absen masuk: ' . $e->getMessage() . ' Silakan coba lagi atau hubungi admin jika masalah berlanjut.');
        }
    }

    public function dailyCheckOut(Request $request)
    {
        try {
            $user = Auth::user();
            $now = now();
            $errors = [];

            $attendanceLog = AttendanceLog::where('user_id', $user->id)
                ->where('log_type', 'attendance')
                ->whereDate('check_in_time', $now->toDateString())
                ->whereNull('check_out_time')
                ->first();

            if (!$attendanceLog) {
                $errors[] = 'Anda belum melakukan absen masuk hari ini atau sudah melakukan absen keluar. Silakan periksa kembali.';
            }

            if (!empty($errors)) {
                return redirect()->back()->with('error', implode(' ', $errors));
            }

            $validated = $request->validate([
                'location' => 'required|string',
                'work_assignment_id' => 'required|exists:work_assignments,id',
                'check_out_photo' => 'required|image', // max 2MB
            ], [
                'location.required' => 'Lokasi absen tidak terdeteksi. Pastikan GPS Anda aktif.',
                'work_assignment_id.required' => 'ID penugasan tidak ditemukan. Silakan hubungi admin.',
                'work_assignment_id.exists' => 'ID penugasan tidak valid. Silakan hubungi admin.',
                'check_out_photo.required' => 'Foto absen keluar wajib diunggah.',
                'check_out_photo.image' => 'File yang diunggah harus berupa gambar.',
            ]);

            $imagePath = $this->saveCompressedImage($request->file('check_out_photo'), 'check_out_photos');

            $attendanceLog->update([
                'check_out_time' => $now,
                'check_out_location' => $validated['location'],
                'check_out_photo' => $imagePath,
            ]);

            $workHours = $now->diffInHours($attendanceLog->check_in_time);

            return redirect()->route('operator-helper.dashboard')->with('success', 'Absen keluar berhasil dicatat pada ' . $now->format('H:i:s') . '. Anda telah bekerja selama sekitar ' . $workHours . ' jam hari ini. Terima kasih atas kerja kerasnya!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Daily check-out validation failed: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Daily check-out failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat melakukan absen keluar: ' . $e->getMessage() . ' Silakan coba lagi atau hubungi admin jika masalah berlanjut.');
        }
    }

    public function checkInForm(WorkAssignment $assignment)
    {
        return view('operator-helper.check-in', compact('assignment'));
    }

    public function checkIn(Request $request, WorkAssignment $assignment)
    {
        try {
            $validated = $request->validate([
                'check_in_photo' => 'required|image',
                'location' => 'required|string',
                'hours_meter_start' => 'required|numeric',
                'hours_meter_start_photo' => 'required|image',
            ]);

            $checkInPhotoPath = $this->saveCompressedImage($request->file('check_in_photo'), 'check_in_photos');
            $hoursMeterStartPhotoPath = $this->saveCompressedImage($request->file('hours_meter_start_photo'), 'hours_meter_photos');

            $attendanceLog = new AttendanceLog([
                'work_assignment_id' => $assignment->id,
                'user_id' => Auth::id(),
                'log_type' => 'work',
                'check_in_time' => now(),
                'check_in_photo' => $checkInPhotoPath,
                'check_in_location' => $validated['location'],
                'hours_meter_start' => $validated['hours_meter_start'],
                'hours_meter_start_photo' => $hoursMeterStartPhotoPath,
            ]);

            $attendanceLog->save();

            return redirect()->route('operator-helper.dashboard')->with('success', 'Check-in berhasil.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Check-in validation failed: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Check-in failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat melakukan check-in. Silakan coba lagi.')->withInput();
        }
    }

    public function checkOutForm(WorkAssignment $assignment)
    {
        $attendanceLog = AttendanceLog::where('work_assignment_id', $assignment->id)
            ->where('user_id', Auth::id())
            ->where('log_type', 'work')
            ->whereNull('check_out_time')
            ->latest()
            ->firstOrFail();

        return view('operator-helper.check-out', compact('assignment', 'attendanceLog'));
    }

    public function checkOut(Request $request, WorkAssignment $assignment)
    {
        try {
            $validated = $request->validate([
                'check_out_photo' => 'required|image',
                'location' => 'required|string',
                'hours_meter_end' => 'required|numeric',
                'hours_meter_end_photo' => 'required|image',
                'field_condition' => 'nullable|string',
            ]);

            $attendanceLog = AttendanceLog::where('work_assignment_id', $assignment->id)
                ->where('user_id', Auth::id())
                ->where('log_type', 'work')
                ->whereNull('check_out_time')
                ->latest()
                ->firstOrFail();

            $checkOutPhotoPath = $this->saveCompressedImage($request->file('check_out_photo'), 'check_out_photos');
            $hoursMeterEndPhotoPath = $this->saveCompressedImage($request->file('hours_meter_end_photo'), 'hours_meter_photos');

            $attendanceLog->update([
                'check_out_time' => now(),
                'check_out_photo' => $checkOutPhotoPath,
                'check_out_location' => $validated['location'],
                'hours_meter_end' => $validated['hours_meter_end'],
                'hours_meter_end_photo' => $hoursMeterEndPhotoPath,
                'field_condition' => $validated['field_condition'],
            ]);

            return redirect()->route('operator-helper.dashboard')->with('success', 'Check-out berhasil.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Check-out validation failed: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Check-out failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat melakukan check-out. Silakan coba lagi.')->withInput();
        }
    }
    public function updateHoursMeter(Request $request, AttendanceLog $log, $type)
    {
        $request->validate([
            'hours_meter' => 'required|numeric|min:0'
        ]);

        $field = $type === 'start' ? 'hours_meter_start' : 'hours_meter_end';

        $log->update([
            $field => $request->hours_meter
        ]);

        return response()->json(['success' => true]);
    }
}
