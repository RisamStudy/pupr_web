<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartrackVehicle;
use App\Services\CartrackVehicleServices;

class CartrackVehicleController extends Controller
{
    protected $cartrackVehicleService;

    public function __construct(CartrackVehicleServices $cartrackVehicleService)
    {
        $this->cartrackVehicleService = $cartrackVehicleService;
    }

    public function index()
    {
        $cartrack_vehicles = CartrackVehicle::simplePaginate(10);
        return view('cartrack-vehicle.index', compact('cartrack_vehicles'));
    }

    public function syncCartrack(Request $request)
    {
        // Logic to sync Cartrack data
        $result = $this->cartrackVehicleService->syncCartrackData();

        try {
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
            ]);
        }
    }

    public function syncCartrackWithHeavyEquipment(Request $request)
    {
        // Logic to sync Cartrack data with Heavy Equipment
        $result = $this->cartrackVehicleService->syncCartrackWithHeavyEquipment();

        try {
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
            ]);
        }
    }
}
