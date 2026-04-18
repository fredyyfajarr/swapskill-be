<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matikan SkillSeeder karena data skill sudah ada di database
        // $this->call(SkillSeeder::class);

        // 2. Buat 20 Mahasiswa palsu
        \App\Models\User::factory(20)->create();

        // 3. Buat 50 Tawaran Barter palsu
        \App\Models\Post::factory(50)->create();
    }
}
