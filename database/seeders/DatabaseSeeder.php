<?php

namespace Database\Seeders;

use App\Enums\Days;
use App\Enums\UserRole;
use App\Models\ClassMember;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $commonSubjects = [
            ['name' => 'Matematika', 'code' => 'MAT-001'],
            ['name' => 'Bahasa Indonesia', 'code' => 'BIN-002'],
            ['name' => 'Bahasa Inggris', 'code' => 'BIG-003'],
            ['name' => 'IPA', 'code' => 'IPA-004'],
            ['name' => 'IPS', 'code' => 'IPS-005'],
        ];

        foreach ($commonSubjects as $subject) {
            Subject::factory()
                ->withDescription()
                ->create([
                    'name' => $subject['name'],
                    'code' => $subject['code'],
                ]);
        }

        $teachers = User::factory(10)->guru()->create();
        $subjects = Subject::select('id')->get();
        $classrooms = Classroom::factory(5)->create();

        $classrooms->each(function ($classroom) use ($teachers, $subjects) {
            $waliKelas = User::factory()->waliKelas()->create();
            ClassMember::factory()
                ->waliKelas()
                ->create([
                    'classroom_id' => $classroom->id,
                    'user_id' => $waliKelas->id,
                ]);

            $ketuaKelas = User::factory()->ketuaKelas()->create();
            ClassMember::factory()
                ->ketuaKelas()
                ->create([
                    'classroom_id' => $classroom->id,
                    'user_id' => $ketuaKelas->id,
                ]);

            User::factory(10)->siswa()->create()->each(function ($user) use ($classroom) {
                ClassMember::factory()->create([
                    'classroom_id' => $classroom->id,
                    'user_id' => $user->id,
                ]);
            });

            Schedule::factory(5)
                ->create([
                    'classroom_id' => $classroom->id,
                    'subject_id' => $subjects->random()->id,
                    'teacher_id' => $teachers->random()->id,
                ]);
        });
    }
}
