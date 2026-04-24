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
        Schema::create('vehicle_positions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trip_id')->nullable();
            $table->foreignId('vehicle_id');
            $table->string('registration')->nullable();
            $table->string('chassis_number')->nullable();
            $table->bigInteger('terminal_id')->nullable();
            $table->string('terminal_serial')->nullable();

            $table->dateTime('start_timestamp')->nullable();
            $table->dateTime('end_timestamp')->nullable();

            $table->double('start_latitude', 15, 8)->nullable();
            $table->double('start_longitude', 15, 8)->nullable();
            $table->double('end_latitude', 15, 8)->nullable();
            $table->double('end_longitude', 15, 8)->nullable();

            $table->integer('trip_duration_seconds')->nullable();
            $table->integer('idle_time_seconds')->nullable();
            $table->double('trip_distance')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_positions');
    }
};
