<?php

namespace App\Imports;

use App\Enums\Days;
use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ScheduleImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari classroom dengan LIKE
        $classroom = Classroom::where('name', 'like', '%' . trim($row['nama_kelas']) . '%')->first();
        if (!$classroom) {
            throw new \Exception("Kelas {$row['nama_kelas']} tidak ditemukan");
        }

        // Cari subject dengan LIKE
        $subject = Subject::where('name', 'like', '%' . trim($row['nama_pelajaran']) . '%')->first();
        if (!$subject) {
            throw new \Exception("Mata pelajaran {$row['nama_pelajaran']} tidak ditemukan");
        }

        // Cari teacher dengan LIKE dan role guru/wali_kelas
        $teacher = User::where('name', 'like', '%' . $row['nama_guru'] . '%')
            ->whereIn('role', ['guru', 'wali_kelas'])
            ->first();

        if (!$teacher) {
            throw new \Exception("Guru {$row['nama_guru']} tidak ditemukan atau bukan guru/wali kelas");
        }

        return new Schedule([
            'classroom_id' => $classroom->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'day' => Days::from(strtolower($row['hari'])),
            'start_time' => Carbon::instance(Date::excelToDateTimeObject($row['waktu_mulai']))->format('H:i'),
            'end_time' => Carbon::instance(Date::excelToDateTimeObject($row['waktu_berakhir']))->format('H:i'),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nama_kelas' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Classroom::where('name', 'like', '%' . trim($value) . '%')->exists();
                    if (!$exists) {
                        $fail("Kelas '{$value}' tidak ditemukan");
                    }
                }
            ],
            '*.nama_pelajaran' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Subject::where('name', 'like', '%' . trim($value) . '%')->exists();
                    if (!$exists) {
                        $fail("Mata pelajaran '{$value}' tidak ditemukan");
                    }
                }
            ],
            '*.nama_guru' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = User::where('name', 'like', '%' . trim($value) . '%')
                        ->whereIn('role', ['guru', 'wali_kelas'])
                        ->exists();
                    if (!$exists) {
                        $fail("Guru '{$value}' tidak ditemukan atau bukan guru/wali kelas");
                    }
                }
            ],
            '*.hari' => [
                'required',
                function ($attribute, $value, $fail) {
                    $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
                    $normalized = strtolower(trim($value));
                    if (!in_array($normalized, $days)) {
                        $fail("Hari harus salah satu dari: " . implode(', ', $days));
                    }
                }
            ],
            '*.waktu_mulai' => 'required',
            '*.waktu_berakhir' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_kelas.required' => 'Nama kelas harus diisi',
            'nama_pelajaran.required' => 'Nama pelajaran harus diisi',
            'nama_guru.required' => 'Nama guru harus diisi',
            'hari.required' => 'Hari harus diisi',
            'waktu_mulai.required' => 'Waktu mulai harus diisi',
            'waktu_mulai.date_format' => 'Format waktu mulai harus HH:MM',
            'waktu_berakhir.required' => 'Waktu berakhir harus diisi',
            'waktu_berakhir.date_format' => 'Format waktu berakhir harus HH:MM',
            'waktu_berakhir.after' => 'Waktu berakhir harus setelah waktu mulai',
        ];
    }
}