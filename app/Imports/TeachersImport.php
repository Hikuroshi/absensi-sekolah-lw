<?php

namespace App\Imports;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeachersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'name' => $row['nama'],
            'username' => $row['username'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password123'),
            'role' => UserRole::GURU,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nama' => 'required|string|max:255',
            '*.username' => 'required|string|max:255|unique:users,username',
            '*.email' => 'required|email|max:255|unique:users,email',
            '*.password' => 'nullable|string|min:6',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'nama.required' => 'Nama lengkap harus diisi',
            'password.min' => 'Password minimal 6 karakter',
        ];
    }
}
