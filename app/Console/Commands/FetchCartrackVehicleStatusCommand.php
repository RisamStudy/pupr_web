<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\CartrackVehicleStatus;

class FetchCartrackVehicleStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cartrack:vehicle-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch list status kendaraan dari Cartrack API dan simpan ke database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Mulai fetch status kendaraan dari Cartrack...');

        try {

            $yesterday = now()->subDay();

            // $startDate = '2025-09-22 00:00:00';
            // $endDate   = '2025-09-28 23:59:59';

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . config('services.cartrack.token'),
            ])->get('https://fleetapi-id.cartrack.com/rest/vehicles/status');

            if ($response->failed()) {
                $this->error('Gagal fetch data dari Cartrack: ' . $response->body());
                return Command::FAILURE;
            }

            $data = $response->json();

            foreach ($data['data'] as $status) {
                CartrackVehicleStatus::updateOrCreate(
                    ['event_ts' => Carbon::parse($status['event_ts'])->format('Y-m-d H:i:s')],
                    [
                        'cartrack_vehicle_id'       => $status['vehicle_id'],
                        'vext'                      => $status['vext'],
                        'ignition'                  => $status['ignition'],
                        'fuel_level'                => $status['fuel']['level'],
                    ]
                );
            }

            $this->info('Selesai sync semua kendaraan 🚗');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
