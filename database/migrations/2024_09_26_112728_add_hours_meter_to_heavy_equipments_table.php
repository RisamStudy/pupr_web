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
        Schema::table('heavy_equipments', function (Blueprint $table) {
            $table->integer('hours_meter')->nullable()->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('heavy_equipments', function (Blueprint $table) {
            $table->dropColumn('hours_meter');
        });
    }
};
