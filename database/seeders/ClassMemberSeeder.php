<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Classes;
use App\Models\ClassMember;

class ClassMemberSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classes::all();
        $students = User::where('role', 'siswa')->get()->shuffle();
        $teachers = User::where('role', 'guru')->get()->shuffle();

        // Minimum requirements per class
        $minStudentsPerClass = 10; // Adjust as needed
        $studentsPerClass = max($minStudentsPerClass, (int) floor($students->count() / $classes->count()));

        foreach ($classes as $index => $class) {
            // Assign homeroom teacher (wali kelas)
            if ($teachers->isNotEmpty()) {
                $teacher = $teachers->pop(); // Get and remove a teacher
                ClassMember::create([
                    'class_id' => $class->id,
                    'user_id' => $teacher->id,
                    'role' => 'guru',
                    'is_active' => true,
                ]);
            }

            // Assign class leader (ketua kelas) and students
            $classStudents = $students->splice(0, $studentsPerClass);
            
            if ($classStudents->isNotEmpty()) {
                // First student becomes class leader
                $leader = $classStudents->shift();
                $leader->update(['role' => 'ketua_kelas']);
                
                ClassMember::create([
                    'class_id' => $class->id,
                    'user_id' => $leader->id,
                    'role' => 'ketua_kelas',
                    'is_active' => true,
                ]);

                // Assign remaining students
                foreach ($classStudents as $student) {
                    ClassMember::create([
                        'class_id' => $class->id,
                        'user_id' => $student->id,
                        'role' => 'siswa',
                        'is_active' => true,
                    ]);
                }
            }
        }

        // Handle remaining students if any
        if ($students->isNotEmpty()) {
            $classes->each(function ($class) use (&$students) {
                if ($students->isNotEmpty()) {
                    $student = $students->shift();
                    ClassMember::create([
                        'class_id' => $class->id,
                        'user_id' => $student->id,
                        'role' => 'siswa',
                        'is_active' => true,
                    ]);
                }
            });
        }
    }
}