<?php

namespace App\Http\Controllers;

use App\Enums\Days;
use App\Enums\UserRole;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedule::search(request('search'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.schedule.index', [
            'title' => 'Daftar Jadwal Pelajaran',
            'schedules' => $schedules
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.schedule.form', [
            'title' => 'Tambah Jadwal Pelajaran',
            'classrooms' => Classroom::select('id', 'name')->get(),
            'subjects' => Subject::select('id', 'name')->get(),
            'teachers' => User::where('role', UserRole::GURU)->get(),
            'days' => Days::options(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|uuid|exists:classrooms,id',
            'subject_id' => 'required|uuid|exists:subjects,id',
            'teacher_id' => 'required|uuid|exists:users,id',
            'day' => 'required|in:' . implode(',', Days::values()),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        Schedule::create($validated);
        return redirect()->route('schedule.index')->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        return view('dashboard.schedule.form', [
            'title' => 'Edit Jadwal Pelajaran',
            'schedule' => $schedule,
            'classrooms' => Classroom::select('id', 'name')->get(),
            'subjects' => Subject::select('id', 'name')->get(),
            'teachers' => User::where('role', UserRole::GURU)->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|uuid|exists:classrooms,id',
            'subject_id' => 'required|uuid|exists:subjects,id',
            'teacher_id' => 'required|uuid|exists:users,id',
            'day' => 'required|in:' . implode(',', Days::values()),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule->update($validated);
        return redirect()->route('schedule.index')->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedule.index')->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
