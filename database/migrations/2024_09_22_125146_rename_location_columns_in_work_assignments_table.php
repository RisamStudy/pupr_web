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
            $table->renameColumn('kota', 'city_id');
            $table->renameColumn('kecamatan', 'district_id');
            $table->renameColumn('desa', 'village_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_assignments', function (Blueprint $table) {
            $table->renameColumn('city_id', 'kota');
            $table->renameColumn('district_id', 'kecamatan');
            $table->renameColumn('village_id', 'desa');
        });
    }
};
