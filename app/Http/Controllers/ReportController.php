<?php

namespace App\Http\Controllers;

use App\Models\WorkAssignment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WorkAssignmentExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkAssignment::with(['heavyEquipment', 'assignmentUsers.user', 'city', 'district', 'village']);

        // Filter berdasarkan status jika ada
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal jika ada
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
        }

        $workAssignments = $query->latest()->paginate(15);

        return view('laporan.index', compact('workAssignments'));
    }

    public function export(Request $request)
    {
        return Excel::download(new WorkAssignmentExport($request), 'rekap_pekerjaan.xlsx');
    }
}
