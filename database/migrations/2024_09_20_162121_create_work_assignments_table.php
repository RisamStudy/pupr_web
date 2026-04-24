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
        Schema::create('work_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('heavy_equipment_id')->constrained()->onDelete('cascade');
            $table->foreignId('operator_id')->constrained('users');
            $table->foreignId('helper_id')->constrained('users');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('location');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('project_name');
            $table->integer('expected_duration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_assignments');
    }
};
