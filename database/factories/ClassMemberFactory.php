<?php

namespace Database\Factories;

use App\Models\ClassMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassMemberFactory extends Factory
{
    protected $model = ClassMember::class;

    public function definition(): array
    {
        return [
            'class_id' => null, // diisi di seeder
            'user_id' => null, // diisi di seeder
            'role' => $this->faker->randomElement(['siswa','guru','ketua_kelas']),
            'is_active' => true,
        ];
    }
} 