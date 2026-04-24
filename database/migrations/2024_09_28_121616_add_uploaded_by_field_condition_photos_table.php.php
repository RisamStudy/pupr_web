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
            $table->foreignId('uploaded_by')->constrained('users')->after('photo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_condition_photos', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn('uploaded_by');
        });
    }
};
