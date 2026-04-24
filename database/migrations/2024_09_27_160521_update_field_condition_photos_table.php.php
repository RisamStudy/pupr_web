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
        Schema::table('field_condition_photos', function (Blueprint $table) {
            $table->foreignId('work_assignment_id')->after('id')->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 8)->after('photo_path');
            $table->decimal('longitude', 11, 8)->after('latitude');
            $table->boolean('is_treatment_point')->default(true)->after('longitude');
            $table->integer('order')->after('is_treatment_point');

            // Jika Anda ingin mengubah relasi dari attendance_log ke work_assignment
            $table->dropForeign(['attendance_log_id']);
            $table->dropColumn('attendance_log_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_condition_photos', function (Blueprint $table) {
            $table->dropForeign(['work_assignment_id']);
            $table->dropColumn(['work_assignment_id', 'latitude', 'longitude', 'is_treatment_point', 'order']);

            // Jika Anda ingin mengembalikan relasi ke attendance_log
            $table->foreignId('attendance_log_id')->constrained()->onDelete('cascade');
        });
    }
};
