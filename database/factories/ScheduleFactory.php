<?php

namespace Database\Factories;

use App\Enums\Days;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate random time between 07:00 and 15:00
        $startTime = Carbon::createFromTime(
            fake()->numberBetween(7, 15),
            fake()->randomElement([0, 15, 30, 45])
        );

        // Add 1-2 hours for end time
        $endTime = (clone $startTime)->addHours(fake()->numberBetween(1, 2));

        return [
            'day' => fake()->randomElement(Days::cases())->value,
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime->format('H:i'),
        ];
    }

    // State for specific days
    public function day(Days $day): static
    {
        return $this->state(fn(array $attributes) => [
            'day' => $day->value,
        ]);
    }

    // State for specific time range
    public function timeRange(string $start, string $end): static
    {
        return $this->state(fn(array $attributes) => [
            'start_time' => $start,
            'end_time' => $end,
        ]);
    }
}
