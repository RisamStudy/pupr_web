<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->decimal('hours_meter_start', 8, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->decimal('hours_meter_start', 8, 2)->nullable(false)->change();
        });
    }
};
