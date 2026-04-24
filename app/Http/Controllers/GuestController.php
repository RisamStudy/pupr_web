<?php

namespace App\Http\Controllers;

use App\Models\CartrackVehicle;
use App\Models\HeavyEquipment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\WorkAssignment;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        // Ambil daftar tahun dari pekerjaan
        $years = WorkAssignment::selectRaw('DISTINCT YEAR(start_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Jika tidak ada tahun yang dipilih, gunakan tahun saat ini
        $selectedYear = $request->input('year', date('Y'));

        $totalHeavyEquipments = HeavyEquipment::count();
        $activeProjects = WorkAssignment::where('status', 'Sedang Berlangsung')
            ->whereYear('start_date', $selectedYear)
            ->count();
        $completedProjects = WorkAssignment::where('status', 'Selesai')
            ->whereYear('start_date', $selectedYear)
            ->count();
        $availableEquipments = HeavyEquipment::where('status', 'ready')->count();
        $availableOperators = User::where('status', 'tersedia')->get();

        $heavyEquipments = HeavyEquipment::whereIn('status', [
            'ready',
            'beroperasi',
            'maintenance',
            'rusak'
        ])->get();

        $stillProjects = WorkAssignment::where('status', 'Sedang Berlangsung')
            ->count();
        $endProjects = WorkAssignment::where('status', 'Selesai')
            ->count();

        $ongoingProjects = WorkAssignment::with([
            'heavyEquipment',
            'assignmentUsers.user',
            'village',
            'district',
            'city',
            'fieldConditionPhotos' => function ($query) {
                $query->latest()->take(1);
            }
        ])
            ->where('status', 'Sedang Berlangsung')
            ->whereYear('start_date', $selectedYear)
            ->get();

        $completedProjectsMap = WorkAssignment::where('status', 'Selesai')
            ->whereYear('start_date', $selectedYear)
            ->with(['village', 'district', 'city', 'heavyEquipment', 'assignmentUsers.user', 'fieldConditionPhotos' => function ($query) {
                $query->latest()->take(1);
            }])
            ->get()
            ->map(function ($project) {
                return [
                    'project_name' => $project->project_name,
                    'latitude' => $project->latitude,
                    'longitude' => $project->longitude,
                    'village_name' => $project->village->name ?? 'N/A',
                    'district_name' => $project->district->name ?? 'N/A',
                    'documentation_link' => $project->documentation_link,
                    'city_name' => $project->city->name ?? 'N/A',
                    'image_url' => $project->fieldConditionPhotos->first() ? asset($project->fieldConditionPhotos->first()->photo_path) : null,
                    'heavy_equipment' => [
                        'nomor_lambung' => $project->heavyEquipment->nomor_lambung ?? 'N/A'
                    ],
                    'operators' => $project->assignmentUsers->where('role', 'operator')->map(function ($au) {
                        return $au->user->name;
                    }),
                    'helpers' => $project->assignmentUsers->where('role', 'helper')->map(function ($au) {
                        return $au->user->name;
                    })
                ];
            });

        $ongoingProjectsMap = WorkAssignment::where('status', 'Sedang Berlangsung')
            ->whereYear('start_date', $selectedYear)
            ->with(['village', 'district', 'city', 'heavyEquipment', 'assignmentUsers.user', 'fieldConditionPhotos' => function ($query) {
                $query->latest()->take(1);
            }])
            ->get()
            ->map(function ($project) {
                return [
                    'project_name' => $project->project_name,
                    'latitude' => $project->latitude,
                    'longitude' => $project->longitude,
                    'village_name' => $project->village->name ?? 'N/A',
                    'district_name' => $project->district->name ?? 'N/A',
                    'documentation_link' => $project->documentation_link,
                    'city_name' => $project->city->name ?? 'N/A',
                    'image_url' => $project->fieldConditionPhotos->first() ? asset($project->fieldConditionPhotos->first()->photo_path) : null,
                    'heavy_equipment' => [
                        'nomor_lambung' => $project->heavyEquipment->nomor_lambung ?? 'N/A'
                    ],
                    'operators' => $project->assignmentUsers->where('role', 'operator')->map(function ($au) {
                        return $au->user->name;
                    }),
                    'helpers' => $project->assignmentUsers->where('role', 'helper')->map(function ($au) {
                        return $au->user->name;
                    })
                ];
            });

        $workTypes = WorkAssignment::distinct('tipe_pekerjaan')->pluck('tipe_pekerjaan');

        return view('guest.index', compact(
            'totalHeavyEquipments',
            'activeProjects',
            'completedProjects',
            'availableEquipments',
            'availableOperators',
            'ongoingProjects',
            'stillProjects',
            'endProjects',
            'completedProjectsMap',
            'ongoingProjectsMap',
            'heavyEquipments',
            'workTypes',
            'years',
            'selectedYear'
        ));
    }
    public function project_map_index(Request $request)
    {
        $years = WorkAssignment::selectRaw('DISTINCT YEAR(start_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        $selectedYear = $request->input('year', date('Y'));

        $completedProjects = WorkAssignment::with(['fieldConditionPhotos' => function ($query) {
            $query->latest()->take(1);
        }])
            ->where('status', 'Selesai')
            ->whereYear('start_date', $selectedYear)
            ->get();

        $ongoingProjects = WorkAssignment::with(['fieldConditionPhotos' => function ($query) {
            $query->latest()->take(1);
        }])
            ->where('status', 'Sedang Berlangsung')
            ->whereYear('start_date', $selectedYear)
            ->get();

        $workTypes = WorkAssignment::distinct('tipe_pekerjaan')->pluck('tipe_pekerjaan')->toArray();

        return view('guest.project-map', compact('completedProjects', 'ongoingProjects', 'workTypes', 'years', 'selectedYear'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $projects = WorkAssignment::with(['city', 'district', 'village', 'fieldConditionPhotos' => function ($q) {
            $q->latest()->take(1);
        }])
            ->where('project_name', 'like', "%$query%")
            ->orWhere('alamat', 'like', "%$query%")
            ->orWhereHas('city', function ($q) use ($query) {
                $q->where('name', 'like', "%$query%");
            })
            ->orWhereHas('district', function ($q) use ($query) {
                $q->where('name', 'like', "%$query%");
            })
            ->orWhereHas('village', function ($q) use ($query) {
                $q->where('name', 'like', "%$query%");
            })
            ->get();

        $formattedProjects = $projects->map(function ($project) {
            return [
                'id' => $project->id,
                'project_name' => $project->project_name,
                'alamat' => $project->alamat,
                'latitude' => $project->latitude,
                'longitude' => $project->longitude,
                'documentation_link' => $project->documentation_link,
                'city_name' => $project->city ? $project->city->name : null,
                'district_name' => $project->district ? $project->district->name : null,
                'village_name' => $project->village ? $project->village->name : null,
                'status' => $project->status,
                'tipe_pekerjaan' => $project->tipe_pekerjaan,
                'image_path' => $project->fieldConditionPhotos->isNotEmpty()
                    ? asset($project->fieldConditionPhotos->first()->photo_path)
                    : null
            ];
        });

        return response()->json($formattedProjects);
    }

    public function getProjectYears()
    {
        $years = WorkAssignment::selectRaw('DISTINCT YEAR(start_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json($years);
    }

    public function getProjects(Request $request)
    {
        $query = $request->input('query');
        $year = $request->input('year', date('Y'));

        $projects = WorkAssignment::with(['city', 'district', 'village', 'fieldConditionPhotos' => function ($q) {
            $q->latest()->take(1);
        }])
            ->when($query, function ($q) use ($query) {
                $q->where('project_name', 'like', "%$query%")
                    ->orWhere('alamat', 'like', "%$query%")
                    ->orWhereHas('city', function ($q2) use ($query) {
                        $q2->where('name', 'like', "%$query%");
                    })
                    ->orWhereHas('district', function ($q2) use ($query) {
                        $q2->where('name', 'like', "%$query%");
                    })
                    ->orWhereHas('village', function ($q2) use ($query) {
                        $q2->where('name', 'like', "%$query%");
                    });
            })
            ->when($year, function ($q) use ($year) {
                $q->whereYear('start_date', $year);
            })
            ->get();
        $formattedProjects = $projects->map(function ($project) {
            return [
                'id' => $project->id,
                'project_name' => $project->project_name,
                'alamat' => $project->alamat,
                'latitude' => $project->latitude,
                'longitude' => $project->longitude,
                'documentation_link' => $project->documentation_link,
                'city_name' => $project->city ? $project->city->name : null,
                'district_name' => $project->district ? $project->district->name : null,
                'village_name' => $project->village ? $project->village->name : null,
                'status' => $project->status,
                'tipe_pekerjaan' => $project->tipe_pekerjaan,
                'image_path' => $project->fieldConditionPhotos->isNotEmpty()
                    ? asset($project->fieldConditionPhotos->first()->photo_path)
                    : null
            ];
        });

        return response()->json($formattedProjects);
    }

    public function map()
    {
        return view('guest.map');
    }

    public function project_map_data(Request $request)
    {
        $cartrack_vehicles = CartrackVehicle::all();

        return response()->json($cartrack_vehicles);
    }
}
