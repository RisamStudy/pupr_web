<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cartrack_vehicle_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cartrack_vehicle_id');
            $table->timestampTz('event_ts')->nullable();
            $table->string('vext')->nullable();
            $table->integer('fuel_level')->nullable();
            $table->boolean('ignition')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartrack_vehicle_statuses');
    }
};
