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
        Schema::table('work_assignments', function (Blueprint $table) {
            $table->decimal('start_hours_meter', 10, 2)->nullable();
            $table->decimal('end_hours_meter', 10, 2)->nullable();
            $table->string('start_hours_meter_image')->nullable();
            $table->string('end_hours_meter_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_assignments', function (Blueprint $table) {
            $table->dropColumn(['start_hours_meter', 'end_hours_meter', 'start_hours_meter_image', 'end_hours_meter_image']);
        });
    }
};
