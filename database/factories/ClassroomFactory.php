<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ClassroomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => 'Kelas ' . fake()->randomElement(['A', 'B', 'C', 'D']) .
                ' ' . fake()->randomElement(['X', 'XI', 'XII']),
            'year' => (date('Y') - 1) . '/' . date('Y'),
        ];
    }
}
