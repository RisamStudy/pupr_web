<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Menambahkan pengguna dengan role admin
        User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('admin123456'), // Pastikan untuk mengganti password ini sesuai kebutuhan
            'roles' => ['admin'],
            'types' => [],
            'status' => 'tersedia',
            'email_verified_at' => now(),
        ]);
    }
}
