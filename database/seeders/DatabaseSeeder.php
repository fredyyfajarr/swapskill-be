<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun Admin
        User::firstOrCreate(
            ['email' => 'admin@swapskill.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'nim' => 'ADMIN001',
                'whatsapp_number' => '081234567890',
                'role' => 'admin',
                'is_verified' => true,
            ]
        );

        // Akun Student Terverifikasi
        User::firstOrCreate(
            ['email' => 'student.verified@swapskill.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'nim' => '1301201234',
                'whatsapp_number' => '081298765432',
                'role' => 'student',
                'is_verified' => true,
            ]
        );

        // Akun Student Belum Terverifikasi
        User::firstOrCreate(
            ['email' => 'student.unverified@swapskill.com'],
            [
                'name' => 'Andi Wijaya',
                'password' => Hash::make('password123'),
                'nim' => '1301201235',
                'whatsapp_number' => '081298765433',
                'role' => 'student',
                'is_verified' => false,
            ]
        );
    }
}
