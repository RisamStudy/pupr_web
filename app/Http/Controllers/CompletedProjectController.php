<?php

namespace App\Http\Controllers;

use App\Models\WorkAssignment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompletedProjectController extends Controller
{
    public function index(Request $request)
    {
        // Get available years from start_date
        $years = WorkAssignment::whereNotNull('completion_date')
            ->select(DB::raw('DISTINCT YEAR(start_date) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');
    
        $selectedYear = $request->input('year', $years->first() ?? now()->year);
    
        $completedProjects = WorkAssignment::whereNotNull('completion_date')
            ->whereYear('start_date', $selectedYear)  // Filter berdasarkan tahun mulai
            ->latest('completion_date')
            ->paginate(10);
    
        return view('completed-projects.index', compact('completedProjects', 'years', 'selectedYear'));
    }
    public function show($id)
    {
        $completedProject = WorkAssignment::findOrFail($id);
        $completedProject->load([
            'heavyEquipment',
            'city',
            'district',
            'village',
            'assignmentUsers',
            'attendanceLogs',
            'fieldConditionPhotos'
        ]);

        $operators = $completedProject->assignmentUsers()
            ->whereHas('user', function ($query) {
                $query->whereJsonContains('roles', 'operator');
            })
            ->where('role', 'operator')
            ->withTrashed()
            ->orderBy('start_date')
            ->get();

        $helpers = $completedProject->assignmentUsers()
            ->whereHas('user', function ($query) {
                $query->whereJsonContains('roles', 'helper');
            })
            ->where('role', 'helper')
            ->withTrashed()
            ->orderBy('start_date')
            ->get();
        $attendanceLogs = $completedProject->attendanceLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            });

        $fieldConditionPhotos = $completedProject->fieldConditionPhotos()
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            });

        return view('completed-projects.show', compact('completedProject','operators','helpers', 'attendanceLogs', 'fieldConditionPhotos'));
    }

    public function destroy($id)
    {
        $completedProject = WorkAssignment::whereNotNull('completion_date')
            ->findOrFail($id);

        $completedProject->delete();

        return redirect()->route('completed-projects.index')
            ->with('success', 'Completed project deleted successfully.');
    }
}
