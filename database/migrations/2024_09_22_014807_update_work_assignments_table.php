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
            // Menambahkan kolom-kolom baru
            $table->string('tipe_pekerjaan')->after('project_name');
            $table->text('permasalahan')->after('tipe_pekerjaan')->nullable();
            $table->string('kota')->after('permasalahan');
            $table->string('kecamatan')->after('kota');
            $table->string('desa')->after('kecamatan');
            $table->decimal('panjang_penanganan', 8, 2)->after('desa');

            // Mengubah kolom 'location' menjadi 'alamat'
            $table->renameColumn('location', 'alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_assignments', function (Blueprint $table) {
            // Menghapus kolom-kolom baru
            $table->dropColumn(['tipe_pekerjaan', 'permasalahan', 'kota', 'kecamatan', 'desa', 'panjang_penanganan']);

            // Mengembalikan nama kolom 'alamat' menjadi 'location'
            $table->renameColumn('alamat', 'location');
        });
    }
};
