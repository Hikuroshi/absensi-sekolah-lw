<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceType;
use App\Models\Schedule;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;

class TeacherAttendanceController extends Controller
{
    public function index()
    {
        $user_class_id = auth()->user()->classrooms()->first()?->id;
        $today_schedules = Schedule::search(request('search'))
            ->where('day', now()->translatedFormat('l'))
            ->where('classroom_id', $user_class_id)
            ->get();

        return view('dashboard.teacher-attendance.index', [
            'title' => 'Absen Kehadiran Guru',
            'today_schedules' => $today_schedules,
        ]);
    }

    public function create(Schedule $schedule)
    {
        $today_attendance = null;

        if ($schedule->is_teacher_attended_today) {
            $today_attendance = TeacherAttendance::whereToday('created_at')->where('schedule_id', $schedule->id)->first();
        }

        $teacher = $schedule->teacher;

        return view('dashboard.teacher-attendance.form', [
            'title' => 'Absen Guru - ' . $teacher->name,
            'statuss' => AttendanceType::options(),
            'schedule' => $schedule,
            'teacher' => $teacher,
            'old_attendance' => $today_attendance,
        ]);
    }

    public function store(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'note' => 'nullable|string',
        ]);

        TeacherAttendance::create([
            'schedule_id' => $schedule->id,
            'classroom_id' => $schedule->classroom_id,
            'user_id' => $schedule->teacher->id,
            'author_id' => auth()->id(),
            'status' => $validated['status'],
            'note' => $attendance['note'] ?? null,
        ]);

        return redirect()->route('teacher-attendance.index')->with('success', 'Absensi berhasil disimpan.');
    }

    public function update(Request $request, TeacherAttendance $teacherAttendance)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $teacherAttendance->update([
            'author_id' => auth()->id(),
            'status' => $validated['status'],
            'note' => $attendance['note'] ?? null,
        ]);

        return redirect()->route('teacher-attendance.index')->with('success', 'Absensi berhasil diperbarui.');
    }
}
