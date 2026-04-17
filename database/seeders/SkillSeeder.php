<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['name' => 'Laravel', 'category' => 'Backend'],
            ['name' => 'Next.js', 'category' => 'Frontend'],
            ['name' => 'React', 'category' => 'Frontend'],
            ['name' => 'Flutter', 'category' => 'Mobile'],
            ['name' => 'Desain UI/UX', 'category' => 'Design'],
            ['name' => 'Database SQL', 'category' => 'Database'],
            ['name' => 'Python/Data Science', 'category' => 'Data'],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }
}
