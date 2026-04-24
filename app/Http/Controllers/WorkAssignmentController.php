<?php

namespace App\Http\Controllers;

use App\Models\WorkAssignment;
use App\Models\HeavyEquipment;
use App\Models\User;
use App\Models\AssignmentUser;
use App\Models\AttendanceLog;
use App\Models\CompletedProject;
use App\Models\FieldConditionPhoto;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;
use Illuminate\Support\Facades\Validator;
class WorkAssignmentController extends Controller
{
    public function index(Request $request)
    {
         // Get available years from work assignments
        $years = WorkAssignment::select(DB::raw('DISTINCT YEAR(start_date) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

        // If no year selected, use current year
        $selectedYear = $request->input('year', now()->year);

        $query = WorkAssignment::with(['heavyEquipment', 'assignmentUsers.user', 'city', 'district'])
            ->whereYear('start_date', $selectedYear);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('project_name', 'like', "%$search%")
                ->orWhere('tipe_pekerjaan', 'like', "%$search%")
                ->orWhere('permasalahan', 'like', "%$search%")
                ->orWhere('alamat', 'like', "%$search%")
                ->orWhereHas('city', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhereHas('district', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhereHas('heavyEquipment', function($q) use ($search) {
                    $q->where('nomor_lambung', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%");
                })
                ->orWhereHas('assignmentUsers.user', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            });
        }

        if ($request->has('status') && $request->input('status') !== '' && $request->input('status') !== null) {
            $status = $request->input('status');
            $query->where('status', $status);
        }

        // Paginate dengan appends untuk mempertahankan parameter URL
        $workAssignments = $query->latest()->paginate(10)->appends($request->except('page'));

        return view('work_assignments.index', compact('workAssignments', 'years', 'selectedYear'));
    }

    public function create()
    {
        $cities = City::all();
        $heavyEquipments = HeavyEquipment::all();

        $users = User::where(function ($query) {
                $query->whereJsonContains('roles', 'operator')
                    ->orWhereJsonContains('roles', 'helper');
            })
            ->where('status', 'tersedia')
            ->whereJsonDoesntContain('roles', 'admin')
            ->get();

        return view('work_assignments.create', compact('heavyEquipments', 'users', 'cities'));
    }

    public function store(Request $request)
    {

        try{
            $validatedData = $request->validate([
                'project_name' => 'required|string',
                'heavy_equipment_id' => 'required|exists:heavy_equipments,id',
                'operator_id' => 'required|exists:users,id',
                'helper_id' => 'required|exists:users,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'tipe_pekerjaan' => 'required|string',
                'permasalahan' => 'nullable|string',
                'expected_duration' => 'required|integer',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'alamat' => 'nullable|string',
                'city_id' => 'required|exists:indonesia_cities,code',
                'district_id' => 'required|exists:indonesia_districts,code',
                'village_id' => 'required|exists:indonesia_villages,code',
                'panjang_penanganan' => 'nullable|numeric',
                'documentation_link' => 'nullable|url',
            ]);

            // Menentukan status pekerjaan
            $today = now()->startOfDay();
            $startDate = \Carbon\Carbon::parse($validatedData['start_date'])->startOfDay();
            $status = $startDate->lte($today) ? 'Sedang Berlangsung' : 'Belum Dimulai';

            $workAssignment = WorkAssignment::create(array_merge($validatedData, ['status' => $status]));

            // Create assignment for operator
            $workAssignment->assignmentUsers()->create([
                'user_id' => $validatedData['operator_id'],
                'role' => 'operator',
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
            ]);

            // Create assignment for helper
            $workAssignment->assignmentUsers()->create([
                'user_id' => $validatedData['helper_id'],
                'role' => 'helper',
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
            ]);

            $heavyEquipments = HeavyEquipment::find($validatedData['heavy_equipment_id']);
            $heavyEquipments ->update([
                'status' => 'beroperasi',
                'current_location' => $validatedData['alamat'],
                'current_latitude' => $validatedData['latitude'],
                'current_longitude' => $validatedData['longitude'],
            ]);

            $operators =  User::find($validatedData['operator_id']);
            $operators->update([
                'status' => 'bertugas',
            ]);

            $helpers =  User::find($validatedData['helper_id']);
            $helpers->update([
                'status' => 'bertugas',
            ]);


            return redirect()->route('work-assignments.index')->with('success', 'Work Assignment created successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            return back()->withInput()->with('error', 'Failed to create user. Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $workAssignment = WorkAssignment::findOrFail($id);
        $workAssignment->load([
            'heavyEquipment',
            'city',
            'district',
            'village',
            'assignmentUsers',
            'attendanceLogs',
            'fieldConditionPhotos'
        ]);

        $operators = $workAssignment->assignmentUsers()
            ->whereHas('user', function ($query) {
                $query->whereJsonContains('roles', 'operator');
            })
            ->where('role', 'operator')
            ->withTrashed()
            ->orderBy('start_date')
            ->get();

        $helpers = $workAssignment->assignmentUsers()
            ->whereHas('user', function ($query) {
                $query->whereJsonContains('roles', 'helper');
            })
            ->where('role', 'helper')
            ->withTrashed()
            ->orderBy('start_date')
            ->get();

        $attendanceLogs = $workAssignment->attendanceLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            });

        $fieldConditionPhotos = $workAssignment->fieldConditionPhotos()
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            });

        return view('work_assignments.show', compact('workAssignment', 'operators', 'helpers', 'attendanceLogs', 'fieldConditionPhotos'));
    }

    public function edit(WorkAssignment $workAssignment)
    {
        $heavyEquipments = HeavyEquipment::where('status', 'ready')
            ->orWhere('id', $workAssignment->heavy_equipment_id)
            ->get();

        // Get all users with operator or helper role
        $allUsers = User::where(function ($query) {
            $query->whereJsonContains('roles', 'operator')
                ->orWhereJsonContains('roles', 'helper');
        })->get();

        // Get IDs of users already assigned to this work assignment
        $assignedUserIds = $workAssignment->assignmentUsers->pluck('user_id')->toArray();

        // Filter available users (status 'tersedia' and not already assigned)
        $availableUsers = $allUsers->filter(function ($user) use ($assignedUserIds) {
            return $user->status === 'tersedia' && !in_array($user->id, $assignedUserIds);
        });

        $cities = City::all();

        return view('work_assignments.edit', compact('workAssignment', 'heavyEquipments', 'availableUsers', 'cities'));
    }

    public function update(Request $request, WorkAssignment $workAssignment)
    {

        try {
            $rules = [
                'project_name' => 'required|string',
                'heavy_equipment_id' => 'required|exists:heavy_equipments,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'tipe_pekerjaan' => 'required|string',
                'permasalahan' => 'nullable|string',
                'expected_duration' => 'required|integer',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'alamat' => 'nullable|string',
                'city_id' => 'required|exists:indonesia_cities,code',
                'district_id' => 'required|exists:indonesia_districts,code',
                'village_id' => 'required|exists:indonesia_villages,code',
                'panjang_penanganan' => 'nullable|numeric',
                'documentation_link' => 'nullable|url',
                'operator_id' => 'nullable|exists:users,id',
                'helper_id' => 'nullable|exists:users,id',
            ];

            if ($request->filled('operator_id')) {
                $rules['operator_start_date'] = 'required|date';
                $rules['operator_end_date'] = 'required|date|after_or_equal:operator_start_date';
            }

            if ($request->filled('helper_id')) {
                $rules['helper_start_date'] = 'required|date';
                $rules['helper_end_date'] = 'required|date|after_or_equal:helper_start_date';
            }

            $validatedData = $request->validate($rules);

            // Update work assignment
            $workAssignment->update($validatedData);

            // Update heavy equipment status
            $heavyEquipment = HeavyEquipment::find($validatedData['heavy_equipment_id']);
            $heavyEquipment->update([
                'status' => 'beroperasi',
                'current_location' => $validatedData['alamat'],
                'current_latitude' => $validatedData['latitude'],
                'current_longitude' => $validatedData['longitude'],
            ]);

            // Handle operator assignment
            if ($request->filled('operator_id')) {
                $workAssignment->assignmentUsers()->create([
                    'role' => 'operator',
                    'user_id' => $request->operator_id,
                    'start_date' => $request->operator_start_date,
                    'end_date' => $request->operator_end_date,
                ]);

                // Update user status
                User::find($request->operator_id)->update(['status' => 'bertugas']);
            }

            // Handle helper assignment
            if ($request->filled('helper_id')) {
                $workAssignment->assignmentUsers()->create([
                    'role' => 'helper',
                    'user_id' => $request->helper_id,
                    'start_date' => $request->helper_start_date,
                    'end_date' => $request->helper_end_date,
                ]);

                // Update user status
                User::find($request->helper_id)->update(['status' => 'bertugas']);
            }

            // Ambil parameter page dan year dari request
            $params = [
                'page' => $request->input('page'),
                'year' => $request->input('year', $workAssignment->start_date->format('Y')) // Gunakan tahun dari assignment jika tidak ada di request
            ];
            // Filter parameter yang tidak null
            $params = array_filter($params, function($value) {
                return !is_null($value);
            });

            return redirect()->route('work-assignments.index', $params)
                            ->with('success', 'Work Assignment updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            return back()->withInput()->with('error', 'Failed to update work assignment. Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function destroy(WorkAssignment $workAssignment)
    {
        try {
            // Begin a database transaction
            DB::beginTransaction();

            // Reset status for the heavy equipment
            $heavyEquipment = $workAssignment->heavyEquipment;
            $heavyEquipment->update([
                'status' => 'ready',
                'current_location' => null,
                'current_latitude' => null,
                'current_longitude' => null,
            ]);

            // Reset status for the operator and helper
            $assignmentUsers = $workAssignment->assignmentUsers;
            foreach ($assignmentUsers as $assignmentUser) {
                $user = $assignmentUser->user;
                $user->update(['status' => 'tersedia']);
            }

            // Soft delete the assignment users
            $workAssignment->assignmentUsers()->delete();

            // Soft delete the work assignment
            $workAssignment->delete();

            // Commit the transaction
            DB::commit();

            return redirect()->route('work-assignments.index')->with('success', 'Work Assignment deleted successfully.');
        } catch (\Exception $e) {
            // If an error occurs, rollback the transaction
            DB::rollBack();
            return redirect()->route('work-assignments.index')->with('error', 'Failed to delete Work Assignment. Error: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $query = WorkAssignment::query();

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('project_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('alamat', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('kota', 'LIKE', "%{$searchTerm}%");
        }

        $workAssignments = $query->latest()->paginate(10);
        return view('work_assignments.index', compact('workAssignments'));
    }

    public function getWorkAssignmentsByLocation()
    {
        $workAssignments = WorkAssignment::select('kota', DB::raw('count(*) as total'))
                            ->groupBy('kota')
                            ->get();

        return response()->json($workAssignments);
    }
    public function manageUsers(WorkAssignment $assignment, $role)
    {
        if (!in_array($role, ['operator', 'helper'])) {
            abort(404);
        }

        $availableUsers = User::whereJsonContains('roles', $role)
            ->whereNotIn('id', $assignment->assignmentUsers()->whereNull('deleted_at')->pluck('user_id'))
            ->get();

        return view('work_assignments.manage_users', compact('assignment', 'role', 'availableUsers'));
    }

    public function addUser(Request $request, WorkAssignment $assignment)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:operator,helper',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        // Periksa apakah end_date melebihi end_date dari assignment
        // $validator->after(function ($validator) use ($request, $assignment) {
        //     if ($request->end_date > $assignment->end_date) {
        //         $validator->errors()->add('end_date', 'Tanggal akhir tidak boleh melebihi tanggal akhir dari penugasan (' . $assignment->end_date . ').');
        //     }
        // });

        // Jika ada kesalahan validasi
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Ambil user
            $user = User::findOrFail($request->user_id);

            // Buat user assignment baru
            $assignmentUser = $assignment->assignmentUsers()->create([
                'user_id' => $request->user_id,
                'role' => $request->role,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            // Update status user
            $user->update(['status' => 'bertugas']);

            $message = ucfirst($request->role) . ' berhasil ditambahkan.';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $assignmentUser
                ]);
            }

            return redirect()->route('work-assignments.manage-users', [
                'assignment' => $assignment->id,
                'role' => $request->role
            ])->with('success', $message);

        } catch (\Exception $e) {
            $errorMessage = 'Terjadi kesalahan saat menambahkan ' . $request->role . ': ' . $e->getMessage();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    public function removeUser(AssignmentUser $assignmentUser, Request $request)
    {
        try {
            $role = $assignmentUser->role;
            $assignmentId = $assignmentUser->work_assignment_id;
            $userId = $assignmentUser->user_id;
            $user = User::find($userId);

            $user->update(['status' => 'tersedia']);

            $assignmentUser->update([
                'end_date' => Carbon::now(),
            ]);

            $assignmentUser->delete(); // This will perform a soft delete

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst($role) . ' berhasil dihapus dari penugasan aktif.'
                ]);
            }

            return redirect()->route('work-assignments.manage-users', [
                'assignment' => $assignmentId,
                'role' => $role
            ])->with('success', ucfirst($role) . ' berhasil dihapus dari penugasan aktif.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus ' . $role . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menghapus ' . $role . ': ' . $e->getMessage());
        }
    }
    public function updateStatus(Request $request, WorkAssignment $workAssignment)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:Belum Dimulai,Sedang Berlangsung,Selesai',
        ]);

        $workAssignment->status = $validatedData['status'];

        if ($validatedData['status'] == "Selesai") {
            $workAssignment->end_date = Carbon::now();
            $workAssignment->completion_date = Carbon::now();

            // Update heavy equipment status
            $heavyEquipment = $workAssignment->heavyEquipment;
            $heavyEquipment->update([
                'status' => 'ready',
                'current_location' => null,
                'current_latitude' => null,
                'current_longitude' => null,
            ]);

            // Update users' status
            $assignmentUsers = $workAssignment->assignmentUsers;
            foreach ($assignmentUsers as $assignmentUser) {
                $assignmentUser->user->update(['status' => 'tersedia']);
            }

            // Calculate total distance
            $attendanceLogs = $workAssignment->attendanceLogs()
                ->whereNotNull('check_in_location')
                ->whereNotNull('check_out_location')
                ->get();

            $totalDistance = 0;

            foreach ($attendanceLogs as $log) {
                $checkInCoords = explode(',', $log->check_in_location);
                $checkOutCoords = explode(',', $log->check_out_location);

                if (count($checkInCoords) == 2 && count($checkOutCoords) == 2) {
                    $segmentDistance = $this->calculateDistance(
                        $checkInCoords[0], $checkInCoords[1],
                        $checkOutCoords[0], $checkOutCoords[1]
                    );
                    $totalDistance += $segmentDistance;
                }
            }

            // Convert distance to kilometers and round to 2 decimal places
            $workAssignment->panjang_penanganan = round($totalDistance / 1000, 2);
        }

        $workAssignment->save();

        return back()->with('success', 'Status pekerjaan berhasil diperbarui.');
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

    public function editHoursMeter(WorkAssignment $workAssignment)
    {
        return view('work_assignments.edit-hours-meter', compact('workAssignment'));
    }

    public function updateHoursMeter(Request $request, WorkAssignment $workAssignment)
    {
        $validatedData = $request->validate([
            'start_hours_meter' => 'nullable|numeric',
            'end_hours_meter' => 'nullable|numeric',
            'start_hours_meter_image' => 'nullable|image|max:2048',
            'end_hours_meter_image' => 'nullable|image|max:2048',
        ]);

        $workAssignment->update($validatedData);
        $heavyEquipment =  $workAssignment->heavyEquipment;
        if($validatedData['end_hours_meter'] && $validatedData['end_hours_meter'] !== null ){
            $heavyEquipment->hours_meter = $validatedData['end_hours_meter'];
        }else{
            $heavyEquipment->hours_meter = $validatedData['start_hours_meter'];
        }
        $heavyEquipment->save();
        if ($request->hasFile('start_hours_meter_image')) {
            $startImage = $request->file('start_hours_meter_image');
            $startImagePath = $startImage->store('hours_meter_images', 'public');
            $workAssignment->start_hours_meter_image = $startImagePath;
        }

        if ($request->hasFile('end_hours_meter_image')) {
            $endImage = $request->file('end_hours_meter_image');
            $endImagePath = $endImage->store('hours_meter_images', 'public');
            $workAssignment->end_hours_meter_image = $endImagePath;
        }

        $workAssignment->save();

        return redirect()->route('work-assignments.show', $workAssignment)->with('success', 'Hours meter updated successfully.');
    }

    public function delete_image($type, $id)
    {
        try {
            DB::beginTransaction();

            switch ($type) {
                case 'check_in':
                case 'check_out':
                case 'hours_meter_start':
                case 'hours_meter_end':
                    $log = AttendanceLog::findOrFail($id);
                    $photoField = $type . '_photo';
                    if ($log->$photoField) {
                        $this->deleteImage($log->$photoField);
                        $log->$photoField = null;
                        $log->save();
                    }
                    break;
                case 'field_condition':
                    $photo = FieldConditionPhoto::findOrFail($id);
                    if ($photo->photo_path) {
                        $this->deleteImage($photo->photo_path);
                        $photo->delete();
                    }
                    break;
                default:
                    throw new \Exception('Invalid image type');
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting image: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete image: ' . $e->getMessage()], 500);
        }
    }

    private function deleteImage($path)
    {
        // Remove the leading "/uploads/" if it exists
        $path = ltrim($path, '/uploads/');

        $fullPath = config('filesystems.uploads_base_path') . '/' . $path;

        Log::info("Attempting to delete file: $fullPath");

        if (file_exists($fullPath)) {
            if (!unlink($fullPath)) {
                throw new \Exception("Failed to delete file: $fullPath");
            }
            Log::info("File successfully deleted: $fullPath");
        } else {
            Log::warning("File not found for deletion: $fullPath");
        }
    }
    
    public function uploadPhotos(Request $request, WorkAssignment $workAssignment)
    {
        try {
            $request->validate([
                'photos' => 'required|array',
                'photos.*' => 'image|mimes:jpeg,png,jpg,gif',
            ]);

            $uploadedPhotos = [];
            foreach ($request->file('photos') as $photo) {
                try {
                    $photoPath = $this->saveCompressedImage($photo, 'field-condition-photos');
                    $fieldConditionPhoto = FieldConditionPhoto::create([
                        'work_assignment_id' => $workAssignment->id,
                        'photo_path' => $photoPath,
                        'uploaded_by' => Auth::id(),
                    ]);
                    $uploadedPhotos[] = $fieldConditionPhoto;
                } catch (\Exception $e) {
                    Log::error('Failed to save photo: ' . $e->getMessage());
                }
            }

            if (empty($uploadedPhotos)) {
                throw new \Exception('No photos were successfully uploaded.');
            }

            return redirect()->route('work-assignments.show', $workAssignment)
                ->with('success', 'Foto berhasil diunggah.');
        } catch (\Exception $e) {
            Log::error('Upload photos failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengunggah foto. Silakan coba lagi.')
                ->withInput();
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

            // Create a temporary path for the file
            $tempPath = $file->getPathname();

            // Load image using GD
            $imageInfo = getimagesize($tempPath);
            switch ($imageInfo[2]) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($tempPath);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($tempPath);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($tempPath);
                    break;
                default:
                    throw new \Exception('Unsupported image type');
            }

            // Calculate new dimensions while maintaining aspect ratio
            $width = imagesx($image);
            $height = imagesy($image);
            $targetWidth = 800;
            $targetHeight = intval($height * ($targetWidth / $width));

            // Create new image with new dimensions
            $newImage = imagecreatetruecolor($targetWidth, $targetHeight);

            // Handle transparency for PNG files
            if ($imageInfo[2] === IMAGETYPE_PNG) {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
            }

            // Resize image
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

            // Save image
            $fullPath = "$path/$filename";
            imagewebp($newImage, $fullPath, 80);

            // Clean up
            imagedestroy($image);
            imagedestroy($newImage);

            // Verify the file was actually saved
            if (!file_exists($fullPath)) {
                throw new \Exception("File was not found after saving: $fullPath");
            }

            Log::info("File successfully saved and compressed: $fullPath");

            return "/uploads/$directory/$filename";
        } catch (\Exception $e) {
            Log::error('Error saving compressed image: ' . $e->getMessage());
            Log::error('File details: ' . json_encode($file->getClientOriginalName()));
            throw $e;
        }
    }
    
}
