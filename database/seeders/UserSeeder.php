<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1 admin (hardcode)
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'id_number' => 'ADM001',
        ]);

        // 10 guru
        User::factory(10)->create([
            'role' => 'guru',
        ]);

        // 50 siswa
        $siswa = User::factory(50)->create([
            'role' => 'siswa',
        ]);

        // 5 ketua_kelas (ambil random dari siswa, ubah role)
        $siswaIds = $siswa->pluck('id')->shuffle()->take(5);
        User::whereIn('id', $siswaIds)->update(['role' => 'ketua_kelas']);
    }
} 