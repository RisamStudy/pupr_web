<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartrackVehicle;
use Illuminate\Support\Facades\Validator;
use App\Services\CartrackPowerTakeOffServices;

class CartrackPowerTakeOffController extends Controller
{
    //
    protected $cartrackPowerTakeOffService;

    public function __construct(CartrackPowerTakeOffServices $cartrackPowerTakeOffService)
    {
        $this->cartrackPowerTakeOffService = $cartrackPowerTakeOffService;
    }

    public function index()
    {
        $cartrackPowerTakeOffService = $this->cartrackPowerTakeOffService->getAll();

        $last_sync = '';
        if ($cartrackPowerTakeOffService->isNotEmpty()) {
            $last_sync = $cartrackPowerTakeOffService->max('event_time');
        }

        return view('cartrack-power-take-off.index', compact('cartrackPowerTakeOffService', 'last_sync'));
    }

    public function syncCartrackPowerTakeOff(Request $request)
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

        $result =  $this->cartrackPowerTakeOffService->syncCartrackPowerTakeOff($input);

        try {
            //code...
            if ($result['status'] == 'success') {
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'message' => $result['message'],
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'success' => false,
                    'message' => $result['message'],
                ], 500);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
            ], 500);
        }
    }
}
