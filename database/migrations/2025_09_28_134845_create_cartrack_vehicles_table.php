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
        Schema::create('cartrack_vehicles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_id')->unique();
            $table->bigInteger('terminal_id')->nullable();
            $table->string('terminal_serial')->nullable();
            $table->string('registration')->nullable();
            $table->string('vehicle_name')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->year('model_year')->nullable();
            $table->string('colour')->nullable();
            $table->string('chassis_number')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartrack_vehicles');
    }
};
