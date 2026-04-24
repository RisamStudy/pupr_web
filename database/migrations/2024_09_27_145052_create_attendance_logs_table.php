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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('check_in_time');
            $table->dateTime('check_out_time')->nullable();
            $table->string('check_in_photo');
            $table->string('check_out_photo')->nullable();
            $table->decimal('hours_meter_start', 10, 2);
            $table->decimal('hours_meter_end', 10, 2)->nullable();
            $table->string('hours_meter_start_photo');
            $table->string('hours_meter_end_photo')->nullable();
            $table->string('check_in_location');
            $table->string('check_out_location')->nullable();
            $table->text('field_condition')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
