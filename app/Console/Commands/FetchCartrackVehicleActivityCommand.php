<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\CartrackVehicleActivity;

class FetchCartrackVehicleActivityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cartrack:activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch list trip dari Cartrack API dan simpan ke database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Mulai fetch data kendaraan dari Cartrack...');

        try {
            $page = 1;

            $yesterday = now()->subDay();

            $startDate = $yesterday->startOfDay()->format('Y-m-d H:i:s');
            $endDate   = $yesterday->endOfDay()->format('Y-m-d H:i:s');

            // $startDate = '2025-09-22 00:00:00';
            // $endDate   = '2025-09-28 23:59:59';

            do {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . config('services.cartrack.token'),
                ])->get('https://fleetapi-id.cartrack.com/rest/trips', [
                    'start_timestamp' => $startDate,
                    'end_timestamp' => $endDate,
                    'page' => $page,
                ]);

                if ($response->failed()) {
                    $this->error('Gagal fetch data dari Cartrack: ' . $response->body());
                    return Command::FAILURE;
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

                $this->info("Page {$page} selesai diproses...");

                $page++;
                $lastPage = $data['meta']['last_page'] ?? 1;
            } while ($page <= $lastPage);

            $this->info('Selesai sync semua kendaraan 🚗');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
