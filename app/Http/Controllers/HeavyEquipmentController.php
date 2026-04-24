<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\CartrackVehicle;
use Illuminate\Http\Request;
use App\Models\HeavyEquipment;
use App\Models\HeavyEquipmentIntegration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HeavyEquipmentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $kondisi = $request->input('kondisi');

        $heavyEquipments = HeavyEquipment::query()
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($kondisi, function ($query, $kondisi) {
                return $query->where('kondisi', $kondisi);
            })
            ->paginate(10);

        return view('heavy_equipments.index', compact('heavyEquipments'));
    }

    public function create()
    {
        $cartrackVehicles = CartrackVehicle::whereDoesntHave('heavyEquipment')->get();
        return view('heavy_equipments.create', compact('cartrackVehicles'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'nomor_lambung' => 'required|max:255',
            'status' => 'required|in:beroperasi,ready,maintenance',
            'merek' => 'required|max:255',
            'tahun' => 'required|numeric',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'maintenance_schedule' => 'nullable|date',
            'location' => 'required|max:255',
            'current_location' => 'nullable|max:255',
            'current_latitude' => 'nullable|numeric',
            'current_longitude' => 'nullable|numeric',
            'hours_meter'  => 'nullable|numeric',
            'cartrack_vehicles' => 'nullable|exists:cartrack_vehicles,id',
        ]);

        $heavyEquipment = HeavyEquipment::create($validatedData);

        if ($request->filled('cartrack_vehicles')) {
            HeavyEquipmentIntegration::create([
                'heavy_equipment_id' => $heavyEquipment->id,
                'integratable_id' => $validatedData['cartrack_vehicles'],
                'integratable_type' => CartrackVehicle::class,
            ]);
        }

        return redirect()->route('alat-berat.index')->with('success', 'Alat berat berhasil ditambahkan.');
    }

    public function show($id)
    {
        $heavyEquipment = HeavyEquipment::findOrFail($id);

        // Query dimodifikasi untuk menggabungkan data yang sama
        $hoursMeterHistory = AttendanceLog::whereIn('work_assignment_id', function ($query) use ($id) {
            $query->select('id')
                ->from('work_assignments')
                ->where('heavy_equipment_id', $id);
        })
            ->whereNotNull('hours_meter_start')
            ->whereNotNull('hours_meter_end')
            ->select(
                'work_assignment_id',
                DB::raw('DATE(check_in_time) as date'),
                DB::raw('MIN(hours_meter_start) as start_meter'), // Ambil nilai awal terkecil
                DB::raw('MAX(hours_meter_end) as end_meter'),     // Ambil nilai akhir terbesar
                DB::raw('COUNT(*) as entries_count')              // Hitung jumlah entri
            )
            ->with('workAssignment:id,project_name')
            ->groupBy('work_assignment_id', DB::raw('DATE(check_in_time)'))
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'date' => Carbon::parse($log->date)->format('d/m/Y'),
                    'start_meter' => $log->start_meter,
                    'end_meter' => $log->end_meter,
                    'project_name' => $log->workAssignment->project_name,
                    'selisih' => $log->end_meter - $log->start_meter,
                    'entries_count' => $log->entries_count // Untuk debugging/monitoring
                ];
            });

        return view('heavy_equipments.show', compact('heavyEquipment', 'hoursMeterHistory'));
    }

    public function edit($id)
    {
        $heavyEquipment = HeavyEquipment::find($id);

        if (!$heavyEquipment) {
            # code...
            return redirect()->route('alat-berat.index')->with('error', 'Alat berat tidak ditemukan.');
        }

        $heavyEquipment->load('workAssignments.city', 'workAssignments.district');
        $cartrackVehicles = CartrackVehicle::whereDoesntHave('heavyEquipment')
            ->orWhereHas('heavyEquipment', function ($query) use ($heavyEquipment) {
                $query->where('heavy_equipment_id', $heavyEquipment->id);
            })
            ->get();
        return view('heavy_equipments.edit', compact('heavyEquipment', 'cartrackVehicles'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'nomor_lambung' => 'required|max:255',
            'status' => 'required|in:beroperasi,ready,maintenance,rusak,tidak ada',
            'merek' => 'required|max:255',
            'tahun' => 'required|numeric',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'maintenance_schedule' => 'nullable|date',
            'location' => 'required|max:255',
            'current_location' => 'nullable|max:255',
            'current_latitude' => 'nullable|numeric',
            'current_longitude' => 'nullable|numeric',
            'hours_meter'  => 'nullable|numeric',
        ]);

        $heavyEquipment =  HeavyEquipment::find($id);

        if (!$heavyEquipment) {
            # code...
            return redirect()->route('alat-berat.index')->with('error', 'Alat berat tidak ditemukan.');
        }

        $heavyEquipment->update([
            'name' => $validatedData['name'],
            'nomor_lambung'  => $validatedData['nomor_lambung'],
            'status' => $validatedData['status'],
            'merek' => $validatedData['merek'],
            'tahun' => $validatedData['tahun'],
            'kondisi' => $validatedData['kondisi'],
            'location' => $validatedData['location'],
            'hours_meter' => $validatedData['hours_meter'],
        ]);

        if ($request->filled('cartrack_vehicles')) {
            $heavyEquipment->integrations()->delete();

            HeavyEquipmentIntegration::create([
                'heavy_equipment_id' => $heavyEquipment->id,
                'integratable_id' => $request->input('cartrack_vehicles'),
                'integratable_type' => CartrackVehicle::class,
            ]);
        }

        return redirect()->route('alat-berat.index')->with('success', 'Alat berat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $heavyEquipment = HeavyEquipment::find($id);

        if (!$heavyEquipment) {
            # code...
            return redirect()->route('alat-berat.index')->with('error', 'Alat berat tidak ditemukan.');
        }

        $heavyEquipment->status = 'tidak ada';
        $heavyEquipment->save();

        return redirect()->route('alat-berat.index')->with('success', 'Alat berat berhasil dihapus.');
    }

    public function apiIndex()
    {
        return HeavyEquipment::all();
    }

    public function updateKondisi(Request $request, $id)
    {
        $validatedData = $request->validate([
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
        ]);

        $heavyEquipment =  HeavyEquipment::find($id);

        if (!$heavyEquipment) {
            # code...
            return redirect()->route('alat-berat.index')->with('error', 'Alat berat tidak ditemukan.');
        }

        $heavyEquipment->kondisi = $validatedData['kondisi'];

        $heavyEquipment->save();

        return back()->with('success', 'Kondisi alat berat berhasil diperbarui.');
    }
    public function getHoursMeterHistory($id)
    {
        $hoursMeterHistory = AttendanceLog::whereIn('work_assignment_id', function ($query) use ($id) {
            $query->select('id')
                ->from('work_assignments')
                ->where('heavy_equipment_id', $id);
        })
            ->whereNotNull('hours_meter_start')
            ->whereNotNull('hours_meter_end')
            ->select(
                'check_in_time as date',
                'hours_meter_end as hours_meter',
                'work_assignment_id'
            )
            ->with('workAssignment:id,project_name')
            ->orderBy('check_in_time', 'desc')  // tetap desc untuk mendapatkan 3 data terbaru
            ->take(3)
            ->get()
            ->groupBy(function ($log) {
                return Carbon::parse($log->date)->format('d/m/Y');
            })
            ->map(function ($logs, $date) {
                $maxHoursMeter = $logs->max('hours_meter');
                return [
                    'week' => $date,
                    'hours_meter' => $maxHoursMeter
                ];
            })
            ->values();

        // Balik urutan array sebelum dikirim
        return response()->json($hoursMeterHistory->reverse()->values());
    }

    public function getTrackingData($id)
    {
        try {
            // Ambil work assignments terkait
            $workAssignments = DB::table('work_assignments')
                ->where('heavy_equipment_id', $id)
                ->pluck('id');

            // Query attendance logs
            $attendanceLogs = AttendanceLog::whereIn('work_assignment_id', $workAssignments)
                ->select(
                    'check_in_time as date',
                    DB::raw('TIME(check_in_time) as time'),
                    'check_in_location',
                    'hours_meter_end as hours_meter',
                    'work_assignment_id' // tambahan untuk logging
                )
                ->orderBy('check_in_time')
                ->get();

            // Transform data
            $transformedData = $attendanceLogs->map(function ($log) {
                try {
                    if (!$log->check_in_location) {
                        Log::warning("Missing check_in_location for log", [
                            'date' => $log->date,
                            'work_assignment_id' => $log->work_assignment_id
                        ]);
                        return null;
                    }

                    list($lat, $lon) = explode(',', $log->check_in_location);

                    return [
                        'date' => Carbon::parse($log->date)->format('d/m/Y'),
                        'time' => $log->time,
                        'latitude' => (float) $lat,
                        'longitude' => (float) $lon,
                        'hours_meter' => $log->hours_meter,
                        'work_assignment_id' => $log->work_assignment_id // untuk logging
                    ];
                } catch (\Exception $e) {
                    Log::error("Error transforming log data", [
                        'error' => $e->getMessage(),
                        'log_data' => $log
                    ]);
                    return null;
                }
            })
                ->filter() // Remove null entries
                ->values();

            // Group by date
            $groupedData = $transformedData->groupBy('date');


            // Get last points for each date
            $trackingData = $groupedData->map(function ($group) {
                $lastPoint = $group->last();
                $result = [
                    'date' => $lastPoint['date'],
                    'time' => $lastPoint['time'],
                    'latitude' => $lastPoint['latitude'],
                    'longitude' => $lastPoint['longitude'],
                    'hours_meter' => $lastPoint['hours_meter'],
                    'visit_count' => $group->count(),
                    'work_assignment_id' => $lastPoint['work_assignment_id']
                ];

                return $result;
            })
                ->values();


            return response()->json($trackingData);
        } catch (\Exception $e) {
            Log::error("Error retrieving tracking data", [
                'heavy_equipment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to retrieve tracking data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
