<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\User;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $startHour = $this->faker->numberBetween(7, 14); // Jam pelajaran 07:00 - 14:00
        $startMinute = $this->faker->randomElement([0, 30]);
        $startTime = sprintf('%02d:%02d', $startHour, $startMinute);
        $endTime = date('H:i', strtotime($startTime . ' +90 minutes'));
        return [
            'class_id' => Classes::inRandomOrder()->first()?->id ?? 1,
            'subject_id' => Subject::inRandomOrder()->first()?->id ?? 1,
            'user_id' => User::where('role', 'guru')->inRandomOrder()->first()?->id ?? 1,
            'day' => $this->faker->randomElement($days),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }
} 