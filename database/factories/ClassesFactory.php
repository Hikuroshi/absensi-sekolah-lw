<?php

namespace Database\Factories;

use App\Models\Classes;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassesFactory extends Factory
{
    protected $model = Classes::class;

    public function definition(): array
    {
        return [
            'name' => 'Kelas ' . $this->faker->randomElement(['7A','7B','8A','8B','9A','9B','10A','10B']),
            'academic_year' => $this->faker->randomElement(['2023/2024','2024/2025','2025/2026']),
            'is_active' => true,
        ];
    }
} 