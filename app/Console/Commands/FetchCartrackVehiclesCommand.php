<?php

namespace App\Console\Commands;

use App\Services\CartrackVehicleServices;
use Illuminate\Console\Command;

class FetchCartrackVehiclesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cartrack:vehicles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch list kendaraan dari Cartrack API dan simpan ke database';

    protected $cartrackService;

    public function __construct(CartrackVehicleServices $cartrackService)
    {
        parent::__construct();
        $this->cartrackService = $cartrackService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Mulai fetch data kendaraan dari Cartrack...');

        try {
            // Panggil method syncCartrackData dari service
            $result = $this->cartrackService->syncCartrackData();

            if ($result['success']) {
                $this->info($result['message']);
                $this->info("Total kendaraan yang diproses: {$result['total']}");
                return Command::SUCCESS;
            } else {
                $this->error($result['message']);
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
