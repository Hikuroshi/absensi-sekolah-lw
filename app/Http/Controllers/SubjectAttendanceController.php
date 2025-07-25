<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceType;
use App\Enums\ClassRole;
use App\Enums\SubjectAttendanceStatus;
use App\Models\ClassMember;
use App\Models\Schedule;
use App\Models\SubjectAttendance;
use App\Models\SubjectAttendanceSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectAttendanceController extends Controller
{
    public function index()
    {
        $user_class_id = auth()->user()->classrooms()->first()?->id;
        $today_schedules = Schedule::search(request('search'))
            ->where('day', now()->translatedFormat('l'))
            // ->where('day', 'senin')
            ->where('classroom_id', $user_class_id)
            ->get();

        return view('dashboard.subject-attendance.index', [
            'title' => 'Absen Kehadiran Mata Pelajaran',
            'today_schedules' => $today_schedules,
        ]);
    }

    public function create(Schedule $schedule)
    {
        $old_attendance = null;
        $today_attendance = null;

        if ($schedule->is__subject_attended_today) {
            $today_attendance = SubjectAttendanceSummary::whereToday('created_at')->where('schedule_id', $schedule->id)->first();
            $old_attendance = SubjectAttendance::where('summary_id', $today_attendance->id)->get()->keyBy('class_member_id');
        }

        $members = ClassMember::where('classroom_id', auth()->user()->classrooms()->first()?->id)
            ->whereNot('role', ClassRole::WALI_KELAS)
            ->get();

        return view('dashboard.subject-attendance.form', [
            'title' => 'Absen Pelajaran - ' . $schedule->classroom->name,
            'statuss' => AttendanceType::options(),
            'schedule' => $schedule,
            'members' => $members,
            'old_attendance' => $old_attendance,
            'old_summary_id' => $today_attendance?->id,
        ]);
    }

    public function store(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.status' => 'required|string',
            'attendances.*.note' => 'nullable|string',
        ]);

        $counts = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'pkl' => 0,
        ];

        foreach ($validated['attendances'] as $memberId => $attendance) {
            $status = strtolower($attendance['status']);
            if (array_key_exists($status, $counts)) {
                $counts[$status]++;
            }
        }

        DB::transaction(function () use ($validated, $schedule, $counts) {
            $summary = SubjectAttendanceSummary::create([
                'schedule_id' => $schedule->id,
                'classroom_id' => $schedule->classroom_id,
                'author_id' => auth()->id(),
                'hadir' => $counts['hadir'],
                'izin' => $counts['izin'],
                'sakit' => $counts['sakit'],
                'alpa' => $counts['alpa'],
                'pkl' => $counts['pkl'],
                'status' => SubjectAttendanceStatus::SUDAH_LENGKAP,
            ]);

            foreach ($validated['attendances'] as $memberId => $attendance) {
                SubjectAttendance::create([
                    'summary_id' => $summary->id,
                    'class_member_id' => $memberId,
                    'status' => $attendance['status'],
                    'note' => $attendance['note'] ?? null,
                ]);
            }
        });

        return redirect()->route('subject-attendance.index')->with('success', 'Absensi berhasil disimpan.');
    }

    public function update(Request $request, SubjectAttendanceSummary $summary)
    {
        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.status' => 'required|string',
            'attendances.*.note' => 'nullable|string',
        ]);

        $counts = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'pkl' => 0,
        ];

        foreach ($validated['attendances'] as $memberId => $attendance) {
            $status = strtolower($attendance['status']);
            if (array_key_exists($status, $counts)) {
                $counts[$status]++;
            }
        }

        DB::transaction(function () use ($validated, $summary, $counts) {
            $summary->update([
                'hadir' => $counts['hadir'],
                'izin' => $counts['izin'],
                'sakit' => $counts['sakit'],
                'alpa' => $counts['alpa'],
                'pkl' => $counts['pkl'],
            ]);

            foreach ($validated['attendances'] as $memberId => $attendance) {
                SubjectAttendance::updateOrCreate(
                    [
                        'summary_id' => $summary->id,
                        'class_member_id' => $memberId,
                    ],
                    [
                        'status' => $attendance['status'],
                        'note' => $attendance['note'] ?? null,
                    ]
                );
            }
        });

        return redirect()->route('subject-attendance.index')->with('success', 'Absensi berhasil diperbarui.');
    }
}
