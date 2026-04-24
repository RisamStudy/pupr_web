<?php

namespace App\Http\Controllers;

use App\Models\WorkAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectMapController extends Controller
{
    public function index(Request $request)
    {
        // Get available years from start_date
        $years = WorkAssignment::select(DB::raw('DISTINCT YEAR(start_date) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

        // If no year selected, use current year
        $selectedYear = $request->input('year', now()->year);

        $completedProjects = WorkAssignment::with(['fieldConditionPhotos' => function($query) {
                $query->latest()->take(1);
            }])
            ->where('status', 'Selesai')
            ->whereYear('start_date', $selectedYear)
            ->get();

        $ongoingProjects = WorkAssignment::with(['fieldConditionPhotos' => function($query) {
                $query->latest()->take(1);
            }])
            ->where('status', 'Sedang Berlangsung')
            ->whereYear('start_date', $selectedYear)
            ->get();

        // Mengambil semua tipe pekerjaan yang unik
        $workTypes = WorkAssignment::distinct('tipe_pekerjaan')->pluck('tipe_pekerjaan')->toArray();

        return view('project-map.index', compact('completedProjects', 'ongoingProjects', 'workTypes', 'years', 'selectedYear'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $year = $request->input('year', now()->year);

        $projects = WorkAssignment::with(['city', 'district', 'village', 'fieldConditionPhotos' => function($q) {
                $q->latest()->take(1);
            }])
            ->whereYear('start_date', $year)
            ->where(function($q) use ($query) {
                $q->where('project_name', 'like', "%$query%")
                  ->orWhere('alamat', 'like', "%$query%")
                  ->orWhereHas('city', function ($q) use ($query) {
                      $q->where('name', 'like', "%$query%");
                  })
                  ->orWhereHas('district', function ($q) use ($query) {
                      $q->where('name', 'like', "%$query%");
                  })
                  ->orWhereHas('village', function ($q) use ($query) {
                      $q->where('name', 'like', "%$query%");
                  });
            })
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'project_name' => $project->project_name,
                    'alamat' => $project->alamat,
                    'latitude' => $project->latitude,
                    'longitude' => $project->longitude,
                    'status' => $project->status,
                    'tipe_pekerjaan' => $project->tipe_pekerjaan,
                    'documentation_link' => $project->documentation_link,
                    'city' => $project->city ? $project->city->name : null,
                    'district' => $project->district ? $project->district->name : null,
                    'village' => $project->village ? $project->village->name : null,
                    'image_path' => $project->fieldConditionPhotos->isNotEmpty()
                        ? asset($project->fieldConditionPhotos->first()->photo_path)
                        : null
                ];
            });

        return response()->json($projects);
    }
}
