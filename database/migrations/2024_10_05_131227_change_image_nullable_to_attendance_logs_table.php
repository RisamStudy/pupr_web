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
            $table->string('check_in_photo')->nullable()->change();
            $table->string('check_out_photo')->nullable()->change();
            $table->string('hours_meter_start_photo')->nullable()->change();
            $table->string('hours_meter_end_photo')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->string('check_in_photo')->nullable(false)->change();
            $table->string('check_out_photo')->nullable(false)->change();
            $table->string('hours_meter_start_photo')->nullable(false)->change();
            $table->string('hours_meter_end_photo')->nullable(false)->change();
        });
    }
};
