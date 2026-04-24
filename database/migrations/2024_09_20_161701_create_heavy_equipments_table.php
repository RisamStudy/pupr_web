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
        Schema::create('heavy_equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nomor_lambung');
            $table->enum('status', ['beroperasi', 'ready', 'maintenance']);
            $table->string('merek');
            $table->string('tahun');
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat']);
            $table->date('maintenance_schedule')->nullable();
            $table->date('last_maintenance_date')->nullable();

            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->string('current_location')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heavy_equipments');
    }
};
