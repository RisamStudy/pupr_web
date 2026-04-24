<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Make existing columns nullable
            $table->string('role')->nullable()->change();
            $table->string('type')->nullable()->change();
            // Add new columns for multiple roles and types
            $table->json('roles')->nullable()->after('role');
            $table->json('types')->nullable()->after('type');

        });

    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn('roles');
            $table->dropColumn('types');
            // Make original columns required again
            $table->string('role')->nullable(false)->change();
            $table->string('type')->nullable(false)->change();

        });

    }
};
