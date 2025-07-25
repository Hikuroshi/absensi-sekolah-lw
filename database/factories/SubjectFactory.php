<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->sentence(3),
            'code' => 'SUB-' . fake()->unique()->bothify('??###'),
            'description' => fake()->boolean(70) ? fake()->paragraph() : null,
        ];
    }

    // Optional state for specific cases
    public function withDescription(): static
    {
        return $this->state(fn(array $attributes) => [
            'description' => fake()->paragraph(),
        ]);
    }

    public function withoutDescription(): static
    {
        return $this->state(fn(array $attributes) => [
            'description' => null,
        ]);
    }
}
