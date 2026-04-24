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
        Schema::create('heavy_equipment_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('heavy_equipment_id')->constrained('heavy_equipments')->onDelete('cascade');
            $table->unsignedBigInteger('integratable_id');
            $table->string('integratable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heavy_equipment_integrations');
    }
};
