<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classes;
use App\Models\User;

class ClassesSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua guru dan ketua_kelas
        $gurus = User::where('role', 'guru')->pluck('id')->shuffle();
        $ketuas = User::where('role', 'ketua_kelas')->pluck('id')->shuffle();

        // Buat 5 kelas dummy
        $kelas = Classes::factory(5)->make();
        foreach ($kelas as $i => $k) {
            Classes::create(array_merge($k->toArray(), [
                'is_active' => true,
            ]));
        }
    }
} 