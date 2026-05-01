<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('work_assignments')) {
            return;
        }

        Schema::create('work_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('heavy_equipment_id');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('alamat')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamp('completion_date')->nullable();
            $table->string('project_name');
            $table->integer('expected_duration');
            $table->string('tipe_pekerjaan');
            $table->text('permasalahan')->nullable();
            $table->string('city_id');
            $table->string('district_id');
            $table->string('village_id');
            $table->float('panjang_penanganan')->nullable();
            $table->string('status')->default('Belum Dimulai');
            $table->string('documentation_link')->nullable();
            $table->decimal('start_hours_meter', 10, 2)->nullable();
            $table->decimal('end_hours_meter', 10, 2)->nullable();
            $table->string('start_hours_meter_image')->nullable();
            $table->string('end_hours_meter_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_assignments');
    }
};
