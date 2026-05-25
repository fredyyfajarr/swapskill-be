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
            ['name' => 'PHP', 'category' => 'Backend'],
            ['name' => 'REST API', 'category' => 'Backend'],
            ['name' => 'Node.js', 'category' => 'Backend'],
            ['name' => 'Next.js', 'category' => 'Frontend'],
            ['name' => 'React', 'category' => 'Frontend'],
            ['name' => 'TypeScript', 'category' => 'Frontend'],
            ['name' => 'Tailwind CSS', 'category' => 'Frontend'],
            ['name' => 'Flutter', 'category' => 'Mobile'],
            ['name' => 'Kotlin', 'category' => 'Mobile'],
            ['name' => 'Desain UI/UX', 'category' => 'Design'],
            ['name' => 'Figma', 'category' => 'Design'],
            ['name' => 'Logo Design', 'category' => 'Design'],
            ['name' => 'Database SQL', 'category' => 'Database'],
            ['name' => 'MySQL', 'category' => 'Database'],
            ['name' => 'PostgreSQL', 'category' => 'Database'],
            ['name' => 'Python/Data Science', 'category' => 'Data'],
            ['name' => 'Machine Learning', 'category' => 'Data'],
            ['name' => 'Data Visualization', 'category' => 'Data'],
            ['name' => 'Microsoft Excel', 'category' => 'Office'],
            ['name' => 'Public Speaking', 'category' => 'Communication'],
            ['name' => 'Bahasa Inggris', 'category' => 'Language'],
            ['name' => 'Copywriting', 'category' => 'Writing'],
            ['name' => 'Content Writing', 'category' => 'Writing'],
            ['name' => 'Video Editing', 'category' => 'Creative'],
            ['name' => 'Fotografi', 'category' => 'Creative'],
            ['name' => 'Manajemen Proyek', 'category' => 'Management'],
            ['name' => 'Git & GitHub', 'category' => 'Development Tools'],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(
                ['name' => $skill['name']],
                ['category' => $skill['category']]
            );
        }
    }
}
