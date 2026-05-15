<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SkillSeeder::class);

        User::create([
            'name' => 'Test User',
            'email' => 'test@swapskill.test',
            'password' => Hash::make('password'),
            'nim' => '123456789',
            'whatsapp_number' => '6281234567890',
            'role' => 'student',
            'is_verified' => true,
        ]);

        User::factory(20)->create([
            'is_verified' => true,
        ]);

        Post::factory(50)->create();
    }
}
