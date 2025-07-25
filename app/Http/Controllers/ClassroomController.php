<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classrooms = Classroom::search(request('search'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.classroom.index', [
            'title' => 'Daftar Kelas',
            'classrooms' => $classrooms
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.classroom.form', [
            'title' => 'Tambah Kelas',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|string|max:9|regex:/^\d{4}\/\d{4}$/',
        ]);

        Classroom::create($validated);
        return redirect()->route('classroom.index')->with('success', 'Kelas berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        $ketua_kelass = User::whereHas('classrooms', function ($query) use ($classroom) {
            $query->where('classroom_id', $classroom->getKey())
                ->where(function ($query) {
                    $query->where('role', UserRole::KETUA_KELAS)
                        ->orWhere('role', UserRole::SISWA);
                });
        })->get();

        $wali_kelass = User::where('role', UserRole::GURU)->orWhere('role', UserRole::WALI_KELAS)->get();

        $leader = (object) [
            'ketua_kelas' => $classroom->members()->where('class_member.role', UserRole::KETUA_KELAS)->first(),
            'wali_kelas' => $classroom->members()->where('class_member.role', UserRole::WALI_KELAS)->first()
        ];

        $members = $classroom->members()->search(request('search'))
            ->latest()
            ->paginate(7)
            ->withQueryString();

        return view('dashboard.classroom.show', [
            'title' => 'Detail Anggota ' . $classroom->name,
            'classroom' => $classroom,
            'members' => $members,
            'students' => User::where('role', UserRole::SISWA)->get(),
            'ketua_kelass' => $ketua_kelass,
            'wali_kelass' => $wali_kelass,
            'leader' => $leader,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {
        return view('dashboard.classroom.form', [
            'title' => 'Edit Kelas',
            'classroom' => $classroom,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|string|max:9|regex:/^\d{4}\/\d{4}$/',
        ]);

        $classroom->update($validated);
        return redirect()->route('classroom.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return redirect()->route('classroom.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
