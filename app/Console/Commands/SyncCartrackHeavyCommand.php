<?php

namespace App\Console\Commands;

use App\Models\HeavyEquipment;
use App\Models\CartrackVehicle;
use Illuminate\Console\Command;

class SyncCartrackHeavyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:cartrack-heavy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync HeavyVehicle dengan CartrackVehicle berdasarkan registration & nomor_lambung';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $heavies = HeavyEquipment::all();

        $this->info('Syncing ' . $heavies->count() . ' heavy equipments...');

        foreach ($heavies as $heavy) {
            # code...
            if (!$heavy->nomor_lambung) {
                $this->warn("Heavy Equipment ID {$heavy->id} tidak memiliki nomor_lambung, dilewati.");
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
                $this->info("Linked Heavy {$heavy->nomor_lambung} with Cartrack {$cartrack->registration}");
            }
        }

        $this->info('Syncing completed.');

        return Command::SUCCESS;
    }
}
