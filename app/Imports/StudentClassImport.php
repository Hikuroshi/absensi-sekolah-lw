<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Classroom;
use App\Models\ClassMember;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StudentClassImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. Cari atau buat classroom
            $classroom = Classroom::firstOrCreate(
                ['name' => $row['kelas']],
                ['year' => $row['tahun_ajaran'] ?? date('Y') . '/' . date('Y', strtotime('+1 year'))]
            );

            // 2. Cek apakah user sudah ada
            $user = User::where('email', $row['email'])
                ->orWhere('username', $row['username'])
                ->first();

            if (!$user) {
                // 3. Buat user baru jika belum ada
                $user = User::create([
                    'name' => $row['nama'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'password' => Hash::make($row['password']),
                    'role' => $this->mapRole($row['role']),
                ]);
            }

            // 4. Tambahkan ke class member
            ClassMember::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'classroom_id' => $classroom->id,
                ],
                [
                    'role' => $this->mapRole($row['role']),
                ]
            );
        }
    }

    protected function mapRole($role)
    {
        $roles = [
            'ketua kelas' => 'ketua_kelas',
            'ketua' => 'ketua_kelas',
            'wali kelas' => 'wali_kelas',
            'wali' => 'wali_kelas',
            'siswa' => 'siswa',
        ];

        return $roles[strtolower($role)] ?? 'siswa';
    }

    public function rules(): array
    {
        return [
            '*.nama' => 'required|string|max:255',
            '*.username' => 'required|string|max:255|unique:users,username',
            '*.email' => 'required|email|max:255|unique:users,email',
            '*.password' => 'required|string|min:6',
            '*.role' => 'required|string',
            '*.kelas' => 'required|string|max:100',
            '*.tahun_ajaran' => 'required|string|max:9',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Nama harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'role.required' => 'Role harus diisi',
            'kelas.required' => 'Kelas harus diisi',
            'tahun_ajaran.required' => 'Tahun ajaran harus diisi',
        ];
    }
}