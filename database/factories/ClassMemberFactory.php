<?php

namespace Database\Factories;

use App\Enums\ClassRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassMember>
 */
class ClassMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'role' => ClassRole::SISWA,
        ];
    }

    public function ketuaKelas()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => ClassRole::KETUA_KELAS,
            ];
        });
    }

    public function waliKelas()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => ClassRole::WALI_KELAS,
            ];
        });
    }
}
