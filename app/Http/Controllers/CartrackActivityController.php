<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartrackVehicle;
use App\Models\CartrackVehicleActivity;
use App\Services\CartrackActivityServices;
use Illuminate\Support\Facades\Validator;

class CartrackActivityController extends Controller
{
    protected $cartrackActivityServices;

    public function __construct(CartrackActivityServices $cartrackActivityServices)
    {
        $this->cartrackActivityServices = $cartrackActivityServices;
    }

    public function index()
    {
        $last_sync = '';
        $cartrack_activities = CartrackVehicleActivity::query();
        if ($cartrack_activities) {
            $last_sync = $cartrack_activities->max('end_timestamp');
        }
        $cartrack_activities = $cartrack_activities->with(['cartrack_vehicle.heavyEquipment'])->simplePaginate(10);
        return view('cartrack-activity.index', compact('cartrack_activities', 'last_sync'));
    }

    public function getCartrackVehicles()
    {
        $cartrack_vehicles = CartrackVehicle::with([
            'heavyEquipment',
            'latestActivity',
            'cartrackVehicleActivity',
        ])->get();
        return response()->json($cartrack_vehicles);
    }

    public function cartrackActivities(Request $request)
    {
        $data = CartrackVehicleActivity::where('cartrack_vehicle_id', $request->vehicleId)
            ->where('start_timestamp', '>=', $request->startDate)
            ->where('end_timestamp', '<=', $request->endDate)
            ->orderBy('start_timestamp', 'desc')
            ->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No activities found.'], 200);
        }

        return response()->json($data);
    }

    public function syncCartrackActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_timestamp' => 'required|date',
            'end_timestamp' => 'required|date|after_or_equal:start_timestamp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'success' => false,
                'message' => 'Invalid input data.',
                'errors' => $validator->errors(),
                'data' => null
            ], 422);
        }

        $input['start_timestamp'] = $request->start_timestamp . ' 00:00:00';
        $input['end_timestamp'] = $request->end_timestamp . ' 23:59:59';

        $result = $this->cartrackActivityServices->syncCartrackActivities($input);

        try {
            //code...
            if ($result['success']) {
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'message' => 'Cartrack activities synced successfully.',
                    'data'  => $request->last_sync
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'success' => false,
                    'message' => $result['message'],
                    'data'  => null
                ], 500);
            }
        } catch (\Exception $th) {
            //throw $th;
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyinkronkan data: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
