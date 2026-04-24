<?php

namespace App\Http\Controllers;

use App\Models\CartrackVehicleStatus;
use Illuminate\Http\Request;

class CartrackVehicleStatusController extends Controller
{
    //

    public function cartrackStatus(Request $request)
    {
        $data = CartrackVehicleStatus::when($request->registration, function ($query) use ($request) {
            return $query->whereHas('cartrackVehicle', function ($q) use ($request) {
                $q->where('registration', $request->registration);
            });
        })
            ->where('event_ts', '>=', $request->startDate)
            ->where('event_ts', '<=', $request->endDate)
            ->orderBy('event_ts', 'asc')
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No vehicle statuses found.'
            ], 200);
        }
        return response()->json($data);
    }
}
