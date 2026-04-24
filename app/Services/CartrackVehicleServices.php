<?php

namespace App\Services;

use App\Models\HeavyEquipment;
use App\Models\CartrackVehicle;
use Illuminate\Support\Facades\Http;

class CartrackVehicleServices
{

    public function syncCartrackData()
    {
        try {
            $page = 1;
            $totalVehicles = 0;

            do {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . config('services.cartrack.token'),
                ])->get("https://fleetapi-id.cartrack.com/rest/vehicles", [
                    'page' => $page,
                    'per_page' => 10,
                ]);

                if ($response->failed()) {
                    return [
                        'success' => false,
                        'message' => 'Gagal fetch data dari Cartrack: ' . $response->body(),
                        'total' => 0
                    ];
                }

                $data = $response->json();

                foreach ($data['data'] as $vehicle) {
                    CartrackVehicle::updateOrCreate(
                        ['vehicle_id' => $vehicle['vehicle_id']],
                        [
                            'terminal_id'   => $vehicle['terminal_id'],
                            'terminal_serial'   => $vehicle['terminal_serial'],
                            'registration'  => $vehicle['registration'],
                            'vehicle_name'  => $vehicle['vehicle_name'],
                            'manufacturer'  => $vehicle['manufacturer'],
                            'model'         => $vehicle['model'],
                            'model_year'    => $vehicle['model_year'],
                            'colour'        => $vehicle['colour'],
                            'chassis_number' => $vehicle['chassis_number'],
                        ]
                    );
                    $totalVehicles++;
                }

                $page++;
                $lastPage = $data['meta']['last_page'] ?? 1;
            } while ($page <= $lastPage);

            return [
                'success' => true,
                'message' => 'Selesai sync semua kendaraan 🚗',
                'total' => $totalVehicles
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'total' => 0
            ];
        }
    }

    public function syncCartrackWithHeavyEquipment()
    {
        //
        try {
            //code...
            $heavies = HeavyEquipment::all();

            foreach ($heavies as $heavy) {
                # code...
                if (!$heavy->nomor_lambung) {
                    continue;
                }

                // Normalize nomor_lambung
                $normalized = preg_replace('/[^A-Z0-9]/', '', strtoupper($heavy->nomor_lambung));

                $cartrack = CartrackVehicle::all()->first(function ($c) use ($normalized) {
                    $reg = preg_replace('/[^A-Z0-9]/', '', strtoupper($c->registration));
                    return str_contains($reg, $normalized);
                });

                if ($cartrack) {
                    $heavy->cartrackVehicles()->syncWithoutDetaching([$cartrack->id]);
                }
            }
            return [
                'success' => true,
                'message' => 'Selesai sync Cartrack dengan Heavy Equipment 🚜',
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
            ];
        }
    }
}
