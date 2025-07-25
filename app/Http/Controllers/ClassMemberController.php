<?php

namespace App\Http\Controllers;

use App\Enums\ClassRole;
use App\Enums\UserRole;
use App\Models\ClassMember;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClassMemberController extends Controller
{
    public function leaderUpdate(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'wali_kelas_id' => 'required|uuid|exists:users,id',
            'ketua_kelas_id' => 'required|uuid|exists:users,id',
        ]);

        DB::transaction(function () use ($classroom, $validated) {
            $waliId = $validated['wali_kelas_id'];
            $ketuaId = $validated['ketua_kelas_id'];

            $classroom->members()->wherePivot('role', ClassRole::WALI_KELAS)
                ->orWherePivot('role', ClassRole::KETUA_KELAS)
                ->detach();

            $classroom->members()->detach([$waliId, $ketuaId]);

            $classroom->members()->attach([
                $waliId => ['role' => ClassRole::WALI_KELAS],
                $ketuaId => ['role' => ClassRole::KETUA_KELAS],
            ]);

            User::findOrFail($waliId)->update(['role' => UserRole::WALI_KELAS]);
            User::findOrFail($ketuaId)->update(['role' => UserRole::KETUA_KELAS]);
        });

        return redirect()->back()->with('success', 'Wali kelas dan ketua kelas berhasil diperbarui.');
    }

    public function createStudent(Classroom $classroom)
    {
        $users = User::search(request('search-new-student'))
            ->whereDoesntHave('classrooms')
            ->where('role', UserRole::SISWA)
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $students = $classroom->members()
            ->wherePivot('role', ClassRole::SISWA)
            ->search(request('search-student'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.classroom.create-student', [
            'title' => 'Tambah Siswa ke ' . $classroom->name,
            'classroom' => $classroom,
            'users' => $users,
            'students' => $students,
        ]);
    }

    public function storeStudent(Request $request, Classroom $classroom)
    {
        $decoded = json_decode($request->input('student_ids'), true);

        $validated = Validator::make(
            ['student_ids' => $decoded],
            [
                'student_ids' => 'required|array',
                'student_ids.*' => 'uuid|exists:users,id',
            ]
        )->validate();

        DB::transaction(function () use ($classroom, $validated) {
            $classroom->members()->attach($validated['student_ids'], ['role' => ClassRole::SISWA]);
        });

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function deleteStudent(Classroom $classroom, User $student)
    {
        $classroom->members()->detach($student->getKey());

        return redirect()->back()->with('success', 'Siswa berhasil dihapus dari kelas.');
    }
}
