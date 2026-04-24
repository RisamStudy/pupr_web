<?php

namespace App\Services;

use App\Models\CartrackPowerTakeOff;
use App\Models\CartrackVehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class CartrackPowerTakeOffServices
{
    //
    public function getAll()
    {
        $datas = CartrackPowerTakeOff::simplePaginate(10);

        return $datas;
    }

    public function syncCartrackPowerTakeOff($input)
    {
        $startDate = $input['start_timestamp'] ?? now()->startOfDay()->format('Y-m-d H:i:s');
        $endDate = $input['end_timestamp'] ?? now()->endOfDay()->format('Y-m-d H:i:s');

        $cartrackVehicles = CartrackVehicle::select('vehicle_id', 'registration')->get();

        \DB::beginTransaction();
        try {

            foreach ($cartrackVehicles as $vehicle) {
                $page = 1;

                $url = "https://fleetapi-id.cartrack.com/rest/vehicles/" . $vehicle->registration . "/power-takeoff";

                do {
                    $response = Http::withHeaders([
                        'Authorization' => 'Basic ' . config('services.cartrack.token'),
                    ])->get($url, [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'page' => $page,
                    ]);

                    if ($response->failed()) {
                        return [
                            'success' => false,
                            'status' => 'error',
                            'message' => 'Gagal fetch data dari Cartrack: ' . $response->body() . ' vehicle: ' . $vehicle . ' page: ' . $page . ' timestamp: ' . $startDate . ' - ' . $endDate,
                        ];
                    }

                    $data = $response->json();

                    foreach ($data['data'] as $trip) {
                        CartrackPowerTakeOff::updateOrCreate(
                            ['cartrack_vehicle_id' => $vehicle->vehicle_id, 'event_time' => Carbon::parse($trip['event_time'])],
                            [
                                'cartrack_vehicle_id'           => $vehicle->vehicle_id,
                                'event_time'                    => $trip['event_time'] ? Carbon::parse($trip['event_time']) : null,
                                'status'                        => $trip['status'] ?? null,
                            ]
                        );
                    }

                    $page++;
                    $lastPage = $data['meta']['last_page'] ?? 1;
                } while ($page <= $lastPage);
            }

            \DB::commit();
            return [
                'success' => true,
                'status' => 'success',
                'message' => 'Cartrack activities synced successfully.',
            ];
        } catch (\Exception $e) {
            \DB::rollBack();
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyinkronkan data: ' . $e->getMessage(),
            ];
        }
    }
}
