<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubjectsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Subject([
            'name' => $row['nama'],
            'code' => $row['kode'],
            'description' => $row['deskripsi'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nama' => 'required|string|max:255',
            '*.kode' => 'required|string|max:50|unique:subjects,code',
            '*.deskripsi' => 'nullable|string|max:500',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Nama mata pelajaran harus diisi',
            'kode.required' => 'Kode mata pelajaran harus diisi',
            'kode.unique' => 'Kode mata pelajaran sudah digunakan',
        ];
    }
}
