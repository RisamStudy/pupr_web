<?php

namespace App\Http\Controllers;

use App\Models\HeavyEquipment;
use App\Models\WorkAssignment;
use App\Models\CompletedProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get available years from work assignments
        $years = WorkAssignment::select(DB::raw('DISTINCT YEAR(start_date) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

        // If no year selected, use current year
        $selectedYear = $request->input('year', now()->year);

        // Filter data based on selected year
        $totalHeavyEquipments = HeavyEquipment::count();
        $activeProjects = WorkAssignment::whereYear('start_date', $selectedYear)
            ->where('status', 'Sedang Berlangsung')
            ->count();
        $completedProjects = WorkAssignment::whereYear('start_date', $selectedYear)
            ->where('status', 'Selesai')
            ->count();
        $availableEquipments = HeavyEquipment::where('status', 'ready')->count();

        $heavyEquipments = HeavyEquipment::all();

        $ongoingProjects = WorkAssignment::with(['heavyEquipment', 'assignmentUsers.user'])
            ->whereYear('start_date', $selectedYear)
            ->where('status', 'Sedang Berlangsung')
            ->get();

        $completedProjectsMap = WorkAssignment::with([
                'village', 'district', 'city',
                'fieldConditionPhotos' => function($query) {
                    $query->latest()->take(1);
                }
            ])
            ->whereYear('start_date', $selectedYear)
            ->where('status', 'Selesai')
            ->get();

        $ongoingProjectsMap = WorkAssignment::with([
                'village', 'district', 'city',
                'heavyEquipment', 'assignmentUsers.user',
                'fieldConditionPhotos' => function($query) {
                    $query->latest()->take(1);
                }
            ])
            ->whereYear('start_date', $selectedYear)
            ->where('status', 'Sedang Berlangsung')
            ->get();

        $recentActivities = WorkAssignment::with(['heavyEquipment', 'assignmentUsers.user'])
            ->select('work_assignments.*')
            ->whereYear('start_date', $selectedYear)
            ->selectRaw("
                CASE
                    WHEN status = 'Sedang Berlangsung' THEN 'start'
                    WHEN status = 'Selesai' THEN 'end'
                    ELSE 'update'
                END as type
            ")
            ->selectRaw("
                CASE
                    WHEN status = 'Sedang Berlangsung' THEN 'Memulai pekerjaan'
                    WHEN status = 'Selesai' THEN 'Menyelesaikan pekerjaan'
                    ELSE 'Memperbarui status'
                END as description
            ")
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalHeavyEquipments',
            'activeProjects',
            'completedProjects',
            'availableEquipments',
            'heavyEquipments',
            'ongoingProjects',
            'completedProjectsMap',
            'ongoingProjectsMap',
            'recentActivities',
            'years',
            'selectedYear'
        ));
    }

    public function getEquipmentUsageHistory(Request $request)
    {
        $year = $request->input('year', now()->year);
        $startDate = Carbon::create($year, 1, 1);
        $endDate = Carbon::create($year, 12, 31);

        $usageHistory = WorkAssignment::whereBetween('start_date', [$startDate, $endDate])
            ->select(DB::raw('
                YEAR(start_date) as year,
                WEEK(start_date) as week,
                DATE(DATE_ADD(start_date, INTERVAL(-WEEKDAY(start_date)) DAY)) as week_start,
                COUNT(*) as count
            '))
            ->groupBy('year', 'week', 'week_start')
            ->orderBy('week_start', 'DESC')
            ->get()
            ->map(function($item) {
                $weekStart = Carbon::parse($item->week_start);
                $weekEnd = $weekStart->copy()->addDays(6);
                return [
                    'week' => $weekStart->format('M') == $weekEnd->format('M')
                        ? "{$weekStart->format('d')} - {$weekEnd->format('d')} {$weekStart->format('M')}"
                        : "{$weekStart->format('d M')} - {$weekEnd->format('d M')}",
                    'count' => $item->count,
                    'raw_date' => $item->week_start // untuk sorting
                ];
            })
            ->sortBy('raw_date')
            ->values();

        return response()->json($usageHistory);
    }
}
