<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\CartrackVehicleActivity;

class CartrackActivityServices
{

    public function syncCartrackActivities($input)
    {
        $startDate = $input['start_timestamp'] ?? now()->startOfDay()->format('Y-m-d H:i:s');
        $endDate = $input['end_timestamp'] ?? now()->endOfDay()->format('Y-m-d H:i:s');

        try {
            $page = 1;

            do {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . config('services.cartrack.token'),
                ])->get('https://fleetapi-id.cartrack.com/rest/trips', [
                    'start_timestamp' => $startDate,
                    'end_timestamp' => $endDate,
                    'page' => $page,
                ]);

                if ($response->failed()) {
                    return [
                        'success' => false,
                        'status' => 'error',
                        'message' => 'Gagal fetch data dari Cartrack: ' . $response->body(),
                    ];
                }

                $data = $response->json();

                foreach ($data['data'] as $trip) {
                    CartrackVehicleActivity::updateOrCreate(
                        ['trip_id' => $trip['trip_id']],
                        [
                            'cartrack_vehicle_id'           => $trip['vehicle_id'],
                            'start_timestamp'               => Carbon::parse($trip['start_timestamp'])->format('Y-m-d H:i:s') ?? null,
                            'end_timestamp'                 => Carbon::parse($trip['end_timestamp'])->format('Y-m-d H:i:s') ?? null,
                            'trip_duration'                 => $trip['trip_duration'] ?? null,
                            'trip_duration_seconds'         => $trip['trip_duration_seconds'] ?? null,
                            'start_location'                => $trip['start_location'] ?? null,
                            'end_location'                  => $trip['end_location'] ?? null,
                            'start_odometer'                => $trip['start_odometer'] ?? null,
                            'end_odometer'                  => $trip['end_odometer'] ?? null,
                            'trip_distance'                 => $trip['trip_distance'] ?? null,
                            'max_speed'                     => $trip['max_speed'] ?? null,
                            'idle_time'                     => $trip['idle_time'] ?? null,
                            'idle_time_seconds'             => $trip['idle_time_seconds'] ?? null,
                            'events_idle'                   => $trip['events_idle'] ?? null,
                            'start_coordinates_latitude'    => $trip['start_coordinates']['latitude'] ?? null,
                            'start_coordinates_longitude'   => $trip['start_coordinates']['longitude'] ?? null,
                            'end_coordinates_latitude'      => $trip['end_coordinates']['latitude'] ?? null,
                            'end_coordinates_longitude'     => $trip['end_coordinates']['longitude'] ?? null,
                        ]
                    );
                }

                $page++;
                $lastPage = $data['meta']['last_page'] ?? 1;
            } while ($page <= $lastPage);
            return [
                'success' => true,
                'status' => 'success',
                'message' => 'Cartrack activities synced successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyinkronkan data: ' . $e->getMessage(),
            ];
        }
    }
}
