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
        Schema::create('cartrack_vehicle_activities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trip_id')->unique();
            $table->foreignId('cartrack_vehicle_id');
            $table->timestampTz('start_timestamp')->nullable();
            $table->timestampTz('end_timestamp')->nullable();

            $table->string('trip_duration')->nullable(); // format hh:mm:ss
            $table->integer('trip_duration_seconds')->nullable();

            $table->text('start_location')->nullable();
            $table->text('end_location')->nullable();

            $table->bigInteger('start_odometer')->nullable();
            $table->bigInteger('end_odometer')->nullable();
            $table->integer('trip_distance')->nullable();

            $table->integer('max_speed')->nullable();

            $table->string('idle_time')->nullable();
            $table->integer('idle_time_seconds')->nullable();
            $table->integer('events_idle')->nullable();

            // start_coordinates
            $table->decimal('start_coordinates_latitude', 10, 6)->nullable();
            $table->decimal('start_coordinates_longitude', 10, 6)->nullable();

            // end_coordinates
            $table->decimal('end_coordinates_latitude', 10, 6)->nullable();
            $table->decimal('end_coordinates_longitude', 10, 6)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartrack_vehicle_activities');
    }
};
