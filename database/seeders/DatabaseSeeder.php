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
        $admin = User::firstOrCreate(
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
        $verifiedStudent = User::firstOrCreate(
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
        $unverifiedStudent = User::firstOrCreate(
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

        $this->call([
            SkillSeeder::class,
        ]);

        $skills = \App\Models\Skill::all();
        if ($skills->count() >= 2) {
            \App\Models\Post::firstOrCreate(
                ['user_id' => $verifiedStudent->id, 'description' => 'Saya butuh bantuan belajar React JS, saya bisa ngajarin UI/UX Design Figma.'],
                [
                    'needed_skill_id' => $skills[0]->id,
                    'offered_skill_id' => $skills[1]->id,
                    'status' => 'open'
                ]
            );

            \App\Models\Post::firstOrCreate(
                ['user_id' => $admin->id, 'description' => 'Mencari partner untuk belajar bahasa Inggris percakapan.'],
                [
                    'needed_skill_id' => $skills[1]->id,
                    'offered_skill_id' => $skills[0]->id,
                    'status' => 'open'
                ]
            );
        }
    }
}
