<?php

namespace App\Http\Controllers;

use App\Models\CompletedProject;
use App\Models\WorkAssignment;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function completedProjects()
    {
        $completedProjects = CompletedProject::all();
        return view('maps.completed_projects', compact('completedProjects'));
    }

    public function activeProjects()
    {
        $activeProjects = WorkAssignment::where('end_date', '>', now())->with(['heavyEquipment', 'operator', 'helper'])->get();
        return view('maps.active_projects', compact('activeProjects'));
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $completedProjects = CompletedProject::where('name', 'like', "%{$searchTerm}%")->get();
        $activeProjects = WorkAssignment::where('project_name', 'like', "%{$searchTerm}%")
            ->where('end_date', '>', now())
            ->with(['heavyEquipment', 'operator', 'helper'])
            ->get();

        return view('maps.search_results', compact('completedProjects', 'activeProjects', 'searchTerm'));
    }
}
