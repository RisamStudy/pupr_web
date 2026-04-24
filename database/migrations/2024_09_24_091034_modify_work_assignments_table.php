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
            $table->dropForeign(['operator_id']);
            $table->dropForeign(['helper_id']);
            $table->dropColumn(['operator_id', 'helper_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_assignments', function (Blueprint $table) {
            $table->foreignId('operator_id')->nullable()->constrained('users');
            $table->foreignId('helper_id')->nullable()->constrained('users');
        });
    }
};
