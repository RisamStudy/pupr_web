<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Entis Sutisna', 'type' => 'operator_alat_berat'],
            ['name' => 'Agus Husein Suhartono', 'type' => 'mekanik_alat_berat'],
            ['name' => 'Regie Fauzan Utama', 'type' => 'mekanik_alat_berat'],
            ['name' => 'Achmad Fitradi', 'type' => 'operator_alat_berat'],
            ['name' => 'Dian Adan Avencahya', 'type' => 'operator_alat_berat'],
            ['name' => 'Gusman Arifin', 'type' => 'operator_alat_berat'],
            ['name' => 'Endi Heryanto', 'type' => 'operator_alat_berat'],
            ['name' => 'Doni Hasrantau', 'type' => 'operator_alat_berat'],
            ['name' => 'Riko Agus Maulana', 'type' => 'operator_alat_berat'],
            ['name' => 'Dimas Al\'afgani', 'type' => 'pembantu_operator'],
            ['name' => 'Adi Krisjayadi', 'type' => 'pembantu_operator'],
            ['name' => 'Dayung Bhaktiar', 'type' => 'pembantu_operator'],
            ['name' => 'Mursalin', 'type' => 'pembantu_operator'],
            ['name' => 'Nurikhsan', 'type' => 'pembantu_operator'],
            ['name' => 'Maman Suryaman', 'type' => 'mekanik_alat_berat'],
            ['name' => 'Irwan Nirwana', 'type' => 'mekanik_alat_berat'],
            ['name' => 'Yana Mulyana', 'type' => 'pembantu_mekanik'],
            ['name' => 'Angga Permana', 'type' => 'pembantu_mekanik'],
        ];

        foreach ($users as $user) {
            // Split the name to get the first name for the email
            $firstName = explode(' ', strtolower($user['name']))[0];

            // Set role based on type
            $role = match ($user['type']) {
                'mekanik_alat_berat', 'operator_alat_berat' => 'operator',
                'pembantu_mekanik', 'pembantu_operator' => 'helper',
                default => 'helper',
            };

            User::create([
                'name' => $user['name'],
                'email' => $firstName . '@cimancis.com',
                'password' => Hash::make($firstName . '123'), // Password based on first name for simplicity
                'role' => $role,
                'type' => $user['type'],
                'status' => 'tersedia',
            ]);
        }

        // Add an admin user
        User::create([
            'name' => 'Admin Cimancis',
            'email' => 'admin@cimancis.com',
            'password' => Hash::make('Cimancis1029!@'), // Default admin password
            'role' => 'admin'
        ]);
    }
}
