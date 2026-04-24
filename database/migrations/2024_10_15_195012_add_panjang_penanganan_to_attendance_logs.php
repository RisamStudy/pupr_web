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
            $table->decimal('panjang_penanganan', 10, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropColumn('panjang_penanganan');
        });
    }
};
