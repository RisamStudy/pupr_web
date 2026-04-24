<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkAssignment;
use App\Models\AttendanceLog;
use App\Models\AssignmentUser;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class OperatorHelperController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $currentAssignment = $this->getCurrentAssignment($user);
        $recentActivities = $this->getRecentActivities($user);

        return view('operator-helper.dashboard', compact('currentAssignment', 'recentActivities'));
    }

    public function checkInForm(WorkAssignment $assignment)
    {
        $this->validateAssignment(Auth::user(), $assignment);
        return view('operator-helper.check-in-form', compact('assignment'));
    }

    public function checkIn(Request $request, WorkAssignment $assignment)
    {
        try {
            $validated = $request->validate([
                'check_in_photo' => 'required|image', // max 2MB
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'hours_meter_start' => 'required|numeric',
                'hours_meter_start_photo' => 'required|image', // max 2MB
            ]);

            $checkInPhotoPath = $this->saveCompressedImage($request->file('check_in_photo'), 'check_in_photos');
            $hoursMeterPhotoPath = $this->saveCompressedImage($request->file('hours_meter_start_photo'), 'hours_meter_photos');

            $location = $validated['latitude'] . ',' . $validated['longitude'];

            $attendanceLog = new AttendanceLog([
                'work_assignment_id' => $assignment->id,
                'user_id' => Auth::id(),
                'check_in_time' => now(),
                'check_in_photo' => $checkInPhotoPath,
                'check_in_location' => $location,
                'hours_meter_start' => $validated['hours_meter_start'],
                'hours_meter_start_photo' => $hoursMeterPhotoPath,
            ]);
            $attendanceLog->save();

            // Update the heavy equipment's hours meter
            $heavyEquipment = $assignment->heavyEquipment;
            $heavyEquipment->hours_meter = $validated['hours_meter_start'];
            $heavyEquipment->save();

            return redirect()->route('operator-helper.dashboard')->with('success', 'Check-in berhasil.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Check-in validation failed: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Check-in failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat melakukan check-in. Silakan coba lagi.')->withInput();
        }
    }

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
            Log::error('Target path: ' . $path ?? 'undefined');
            throw $e;
        }
    }


    public function checkOutForm(WorkAssignment $assignment)
    {
        $this->validateAssignment(Auth::user(), $assignment);
        $attendanceLog = $this->getLatestOpenAttendanceLog($assignment);
        return view('operator-helper.check-out-form', compact('assignment', 'attendanceLog'));
    }

    public function checkOut(Request $request, WorkAssignment $assignment)
    {
        try {
            $validated = $request->validate([
                'check_out_photo' => 'required|image',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'hours_meter_end' => 'required|numeric|min:0',
                'hours_meter_end_photo' => 'required|image',
                'field_condition' => 'nullable|string',
            ]);

            $user = Auth::user();
            $this->validateAssignment($user, $assignment);

            $attendanceLog = $this->getLatestOpenAttendanceLog($assignment);

            $checkOutPhotoPath = $this->saveCompressedImage($request->file('check_out_photo'), 'check_out_photos');
            $hoursMeterEndPhotoPath = $this->saveCompressedImage($request->file('hours_meter_end_photo'), 'hours_meter_photos');

            $checkOutLocation = $validated['latitude'] . ',' . $validated['longitude'];

            // Calculate distance
            $checkInCoords = explode(',', $attendanceLog->check_in_location);
            $checkOutCoords = [$validated['latitude'], $validated['longitude']];
            $distance = $this->calculateDistance(
                $checkInCoords[0], $checkInCoords[1],
                $checkOutCoords[0], $checkOutCoords[1]
            );

            // Convert distance to kilometers
            $distanceInKm = round($distance / 1000, 2);

            $attendanceLog->update([
                'check_out_time' => now(),
                'check_out_photo' => $checkOutPhotoPath,
                'check_out_location' => $checkOutLocation,
                'hours_meter_end' => $validated['hours_meter_end'],
                'hours_meter_end_photo' => $hoursMeterEndPhotoPath,
                'field_condition' => $validated['field_condition'],
                'panjang_penanganan' => $distanceInKm,
            ]);

            $assignment->end_hours_meter = $validated['hours_meter_end'];
            $assignment->end_hours_meter_image = $hoursMeterEndPhotoPath;
            $assignment->panjang_penanganan = $assignment->panjang_penanganan + $distanceInKm;
            $assignment->save();

            // Update the heavy equipment's hours meter
            $heavyEquipment = $assignment->heavyEquipment;
            $heavyEquipment->hours_meter = $validated['hours_meter_end'];
            $heavyEquipment->save();

            return redirect()->route('operator-helper.dashboard')->with('success', 'Check-out berhasil.');
        } catch (\Exception $e) {
            Log::error('Check-out failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat melakukan check-out. Silakan coba lagi.')->withInput();
        }
    }

    private function saveImage($file, $directory)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = public_path("uploads/$directory");

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $file->move($path, $filename);

        return "uploads/$directory/$filename";
    }

    private function getCurrentAssignment($user)
    {
        return AssignmentUser::where('user_id', $user->id)
            ->whereHas('user', function ($query) {
                $query->where(function ($query) {
                    $query->whereJsonContains('roles', 'operator')
                        ->orWhereJsonContains('roles', 'helper');
                });
            })
            ->whereHas('workAssignment', function ($query) {
                $query->where('status', 'Sedang Berlangsung');
            })
            ->with('workAssignment.heavyEquipment')
            ->first()
            ->workAssignment ?? null;
    }

    private function getRecentActivities($user)
    {
        return AttendanceLog::where('user_id', $user->id)
            ->with('workAssignment')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    private function validateAssignment($user, $assignment)
    {
        return AssignmentUser::where('work_assignment_id', $assignment->id)
            ->where('user_id', $user->id)
            ->whereHas('user', function ($query) {
                $query->where(function ($query) {
                    $query->whereJsonContains('roles', 'operator')
                        ->orWhereJsonContains('roles', 'helper');
                });
            })
            ->firstOrFail();
    }

    private function getLatestOpenAttendanceLog($assignment)
    {
        return AttendanceLog::where('work_assignment_id', $assignment->id)
            ->where('user_id', Auth::id())
            ->whereNull('check_out_time')
            ->latest()
            ->firstOrFail();
    }
    public function history(Request $request)
    {
        $user = Auth::user();
        $query = WorkAssignment::whereHas('assignmentUsers', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['attendanceLogs' => function ($query) use ($user) {
            $query->where('user_id', $user->id)->orderBy('created_at', 'desc');
        }, 'fieldConditionPhotos']);

        // Apply search filter
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('project_name', 'like', "%{$searchTerm}%");
        }

         // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Apply date range filter
        if ($request->has('date_range') && $request->input('date_range') !== '') {
            $dateRange = explode(' - ', $request->input('date_range'));
            if (count($dateRange) == 2) {
                $startDate = Carbon::createFromFormat('d/m/Y', $dateRange[0])->startOfDay();
                $endDate = Carbon::createFromFormat('d/m/Y', $dateRange[1])->endOfDay();
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            }
        }

        $workAssignments = $query->orderBy('start_date', 'desc')->paginate(10);

        // Append query parameters to pagination links
        $workAssignments->appends($request->all());

        return view('operator-helper.history', compact('workAssignments'));
    }

    public function historyDetail(WorkAssignment $workAssignment)
    {
        $user = Auth::user();
        $attendanceLogs = $workAssignment->attendanceLogs()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($date) {
                return $date->created_at->format('Y-m-d');
            });

        $fieldConditionPhotos = $workAssignment->fieldConditionPhotos()
            ->where('uploaded_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($date) {
                return $date->created_at->format('Y-m-d');
            });

        return view('operator-helper.history-detail', compact('workAssignment', 'attendanceLogs', 'fieldConditionPhotos'));
    }
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters
        $lat1 = deg2rad(floatval($lat1));
        $lon1 = deg2rad(floatval($lon1));
        $lat2 = deg2rad(floatval($lat2));
        $lon2 = deg2rad(floatval($lon2));
        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

}
