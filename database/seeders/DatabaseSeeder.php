<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Skill;
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
        $this->call([
            SkillSeeder::class,
        ]);

        $admin = $this->seedUser([
            'name' => 'Administrator',
            'email' => 'admin@swapskill.com',
            'password' => 'password123',
            'nim' => 'ADMIN001',
            'whatsapp_number' => '081234567890',
            'role' => 'admin',
            'is_verified' => true,
        ]);

        $verifiedStudent = $this->seedUser([
            'name' => 'Budi Santoso',
            'email' => 'student.verified@swapskill.com',
            'password' => 'password123',
            'nim' => '1301201234',
            'whatsapp_number' => '081298765432',
            'role' => 'student',
            'is_verified' => true,
        ]);

        $unverifiedStudent = $this->seedUser([
            'name' => 'Andi Wijaya',
            'email' => 'student.unverified@swapskill.com',
            'password' => 'password123',
            'nim' => '1301201235',
            'whatsapp_number' => '081298765433',
            'role' => 'student',
            'is_verified' => false,
        ]);

        $legacyTestStudent = $this->seedUser([
            'name' => 'Test Student',
            'email' => 'test@swapskill.test',
            'password' => 'password',
            'nim' => 'TEST001',
            'whatsapp_number' => '081200000001',
            'role' => 'student',
            'is_verified' => true,
        ]);

        $skills = Skill::all()->keyBy('name');
        $attachSkills = function (User $user, array $skillNames) use ($skills): void {
            $skillIds = collect($skillNames)
                ->map(fn (string $name) => $skills->get($name)?->id)
                ->filter()
                ->values()
                ->all();

            $user->skills()->sync($skillIds);
        };

        $attachSkills($admin, ['Laravel', 'Next.js', 'Database SQL', 'Manajemen Proyek']);
        $attachSkills($verifiedStudent, ['Laravel', 'React', 'Desain UI/UX', 'Git & GitHub']);
        $attachSkills($unverifiedStudent, ['Microsoft Excel', 'Public Speaking']);
        $attachSkills($legacyTestStudent, ['Next.js', 'Tailwind CSS', 'Figma']);

        $demoProfiles = [
            ['Siti Aminah', 'siti.aminah@swapskill.test', ['Next.js', 'TypeScript', 'Tailwind CSS']],
            ['Raka Pratama', 'raka.pratama@swapskill.test', ['Python/Data Science', 'Machine Learning', 'Data Visualization']],
            ['Dewi Lestari', 'dewi.lestari@swapskill.test', ['Figma', 'Desain UI/UX', 'Logo Design']],
            ['Fajar Nugroho', 'fajar.nugroho@swapskill.test', ['Flutter', 'Kotlin', 'REST API']],
            ['Nadia Putri', 'nadia.putri@swapskill.test', ['Bahasa Inggris', 'Public Speaking', 'Copywriting']],
            ['Yoga Saputra', 'yoga.saputra@swapskill.test', ['MySQL', 'PostgreSQL', 'Database SQL']],
            ['Maya Anggraini', 'maya.anggraini@swapskill.test', ['Video Editing', 'Fotografi', 'Content Writing']],
            ['Rizky Maulana', 'rizky.maulana@swapskill.test', ['PHP', 'Laravel', 'REST API']],
            ['Anisa Rahma', 'anisa.rahma@swapskill.test', ['React', 'Next.js', 'Git & GitHub']],
            ['Bagas Firmansyah', 'bagas.firmansyah@swapskill.test', ['Node.js', 'TypeScript', 'REST API']],
            ['Citra Maharani', 'citra.maharani@swapskill.test', ['Microsoft Excel', 'Data Visualization', 'Manajemen Proyek']],
            ['Dimas Arya', 'dimas.arya@swapskill.test', ['Flutter', 'Figma', 'Desain UI/UX']],
            ['Eka Wulandari', 'eka.wulandari@swapskill.test', ['Bahasa Inggris', 'Content Writing', 'Public Speaking']],
            ['Gilang Ramadhan', 'gilang.ramadhan@swapskill.test', ['Laravel', 'MySQL', 'Git & GitHub']],
            ['Hana Safitri', 'hana.safitri@swapskill.test', ['Fotografi', 'Video Editing', 'Logo Design']],
            ['Irfan Hakim', 'irfan.hakim@swapskill.test', ['Python/Data Science', 'Microsoft Excel', 'Machine Learning']],
            ['Jihan Aulia', 'jihan.aulia@swapskill.test', ['React', 'Tailwind CSS', 'Figma']],
            ['Kevin Andika', 'kevin.andika@swapskill.test', ['Node.js', 'PostgreSQL', 'REST API']],
            ['Laras Permata', 'laras.permata@swapskill.test', ['Copywriting', 'Content Writing', 'Bahasa Inggris']],
            ['Miko Prakoso', 'miko.prakoso@swapskill.test', ['Kotlin', 'Flutter', 'Git & GitHub']],
            ['Naufal Rizal', 'naufal.rizal@swapskill.test', ['Next.js', 'Laravel', 'TypeScript']],
            ['Olivia Kartika', 'olivia.kartika@swapskill.test', ['Desain UI/UX', 'Figma', 'Public Speaking']],
            ['Putra Wijaya', 'putra.wijaya@swapskill.test', ['Database SQL', 'MySQL', 'Python/Data Science']],
            ['Qonita Zahra', 'qonita.zahra@swapskill.test', ['Video Editing', 'Content Writing', 'Fotografi']],
        ];

        $demoUsers = [];
        foreach ($demoProfiles as $index => [$name, $email, $skillNames]) {
            $user = $this->seedUser([
                'name' => $name,
                'email' => $email,
                'password' => 'password123',
                'nim' => 'DEMO' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'whatsapp_number' => '08130000' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'role' => 'student',
                'is_verified' => true,
            ]);

            $attachSkills($user, $skillNames);
            $demoUsers[] = $user;
        }

        $skillId = fn (string $name): int => $skills->get($name)->id;
        $postSeeds = [
            [$verifiedStudent, 'Next.js', 'Laravel', 'Saya ingin memperdalam Next.js untuk frontend dashboard, saya bisa bantu mentoring dasar Laravel dan struktur API.'],
            [$verifiedStudent, 'Bahasa Inggris', 'React', 'Butuh teman latihan speaking untuk presentasi proyek, saya bisa bantu belajar komponen React.'],
            [$verifiedStudent, 'Python/Data Science', 'Desain UI/UX', 'Saya ingin mulai belajar analisis data dengan Python, saya bisa barter review UI dan prototype Figma.'],
            [$legacyTestStudent, 'Laravel', 'Next.js', 'Saya bisa bantu slicing Next.js dan Tailwind, ingin belajar membuat REST API Laravel yang rapi.'],
            [$demoUsers[0], 'Laravel', 'Next.js', 'Mencari mentor Laravel untuk integrasi login API, saya bisa bantu setup Next.js App Router.'],
            [$demoUsers[1], 'React', 'Python/Data Science', 'Saya butuh belajar React untuk visualisasi data, saya bisa ajarkan dasar data cleaning dengan Python.'],
            [$demoUsers[2], 'Git & GitHub', 'Figma', 'Butuh bantuan workflow Git untuk kolaborasi, saya bisa bantu membuat desain wireframe di Figma.'],
            [$demoUsers[3], 'Laravel', 'Flutter', 'Saya ingin belajar backend Laravel untuk aplikasi mobile, saya bisa bantu setup UI Flutter.'],
            [$demoUsers[4], 'Desain UI/UX', 'Bahasa Inggris', 'Butuh masukan desain halaman profil, saya bisa bantu latihan percakapan bahasa Inggris.'],
            [$demoUsers[5], 'React', 'Database SQL', 'Saya ingin belajar React untuk frontend admin, saya bisa bantu normalisasi database SQL.'],
            [$demoUsers[6], 'Next.js', 'Video Editing', 'Butuh bantuan Next.js untuk portfolio online, saya bisa bantu edit video pendek untuk konten.'],
            [$demoUsers[7], 'Figma', 'Laravel', 'Saya bisa bantu debugging Laravel, ingin belajar membuat prototype mobile di Figma.'],
            [$demoUsers[8], 'Laravel', 'React', 'Mencari partner belajar Laravel Sanctum, saya bisa bantu membuat komponen React reusable.'],
            [$demoUsers[9], 'Desain UI/UX', 'Node.js', 'Butuh review UX untuk aplikasi tugas, saya bisa bantu konsep API dengan Node.js.'],
            [$demoUsers[10], 'React', 'Microsoft Excel', 'Saya bisa bantu dashboard Excel, ingin belajar React untuk membuat chart interaktif.'],
            [$demoUsers[11], 'REST API', 'Flutter', 'Mencari partner belajar REST API, saya bisa bantu membangun layout Flutter.'],
            [$demoUsers[12], 'Tailwind CSS', 'Public Speaking', 'Saya ingin belajar Tailwind CSS, saya bisa bantu latihan presentasi proyek.'],
            [$demoUsers[13], 'Next.js', 'MySQL', 'Saya butuh Next.js untuk halaman dashboard, saya bisa bantu query dan relasi MySQL.'],
            [$demoUsers[14], 'React', 'Fotografi', 'Butuh belajar React dasar, saya bisa bantu teknik fotografi produk.'],
            [$demoUsers[15], 'Desain UI/UX', 'Machine Learning', 'Saya ingin masukan desain app data science, saya bisa ajarkan dasar supervised learning.'],
            [$demoUsers[16], 'Laravel', 'Tailwind CSS', 'Mencari bantuan Laravel controller, saya bisa bantu styling responsive dengan Tailwind.'],
            [$demoUsers[17], 'Figma', 'PostgreSQL', 'Butuh belajar design system Figma, saya bisa bantu optimasi query PostgreSQL.'],
            [$demoUsers[18], 'Next.js', 'Copywriting', 'Saya bisa bantu copy landing page, ingin belajar route dan data fetching Next.js.'],
            [$demoUsers[19], 'REST API', 'Kotlin', 'Butuh partner belajar REST API untuk mobile, saya bisa bantu dasar Kotlin Android.'],
            [$demoUsers[20], 'React', 'Laravel', 'Saya bisa bantu Laravel service layer, ingin barter dengan React hooks dan state management.'],
            [$demoUsers[21], 'Git & GitHub', 'Desain UI/UX', 'Saya bisa bantu evaluasi UI, butuh belajar branch dan pull request GitHub.'],
            [$demoUsers[22], 'Figma', 'Database SQL', 'Mencari teman belajar Figma, saya bisa bantu ERD dan SQL join.'],
            [$demoUsers[23], 'Next.js', 'Content Writing', 'Saya bisa bantu struktur artikel blog, ingin belajar Next.js untuk personal web.'],
        ];

        $skillNames = $skills->keys()->values();
        $postTemplates = [
            'Mencari partner belajar %s, saya bisa barter dengan materi %s.',
            'Butuh sesi praktik %s untuk tugas kuliah, saya siap bantu latihan %s.',
            'Ingin meningkatkan skill %s lewat studi kasus kecil, saya bisa ajarkan dasar %s.',
            'Mau belajar %s dari nol sampai bisa buat mini project, saya menawarkan mentoring %s.',
        ];

        foreach ($demoUsers as $index => $user) {
            $needed = $skillNames[($index + 4) % $skillNames->count()];
            $offered = $skillNames[($index + 11) % $skillNames->count()];
            if ($needed === $offered) {
                $offered = $skillNames[($index + 12) % $skillNames->count()];
            }

            $postSeeds[] = [
                $user,
                $needed,
                $offered,
                sprintf($postTemplates[$index % count($postTemplates)], $needed, $offered),
            ];
        }

        foreach ($postSeeds as [$user, $neededSkill, $offeredSkill, $description]) {
            Post::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'description' => $description,
                ],
                [
                    'needed_skill_id' => $skillId($neededSkill),
                    'offered_skill_id' => $skillId($offeredSkill),
                    'status' => 'open',
                ]
            );
        }
    }

    private function seedUser(array $data): User
    {
        return User::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
                'nim' => $data['nim'],
                'whatsapp_number' => $data['whatsapp_number'],
                'role' => $data['role'],
                'is_verified' => $data['is_verified'],
            ]
        );
    }
}
