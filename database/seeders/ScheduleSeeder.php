<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\User;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $startTimes = ['07:00', '08:30', '10:00', '11:30', '13:00'];
        $kelas = Classes::all();
        $subjects = Subject::all();
        $gurus = User::where('role', 'guru')->get();

        foreach ($kelas as $class) {
            // Setiap kelas dapat 4-6 pelajaran per minggu
            $subjectsForClass = $subjects->random(rand(4, 6));
            foreach ($subjectsForClass as $subject) {
                $guru = $gurus->random();
                $day = $days[array_rand($days)];
                $start = $startTimes[array_rand($startTimes)];
                $end = date('H:i', strtotime($start . ' +90 minutes'));
                // Cek unik
                if (!Schedule::where([
                    'class_id' => $class->id,
                    'subject_id' => $subject->id,
                    'user_id' => $guru->id,
                ])->exists()) {
                    Schedule::create([
                        'class_id' => $class->id,
                        'subject_id' => $subject->id,
                        'user_id' => $guru->id,
                        'day' => $day,
                        'start_time' => $start,
                        'end_time' => $end,
                    ]);
                }
            }
        }
    }
}