<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceType;
use App\Enums\ClassRole;
use App\Enums\SessionType;
use App\Enums\StudentAttendanceStatus;
use App\Models\ClassMember;
use App\Models\StudentAttendance;
use App\Models\StudentAttendanceSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentAttendanceController extends Controller
{

    public function index()
    {
        $user_class = auth()->user()->classrooms()->first();

        $today_summary = StudentAttendanceSummary::whereToday('created_at')
            ->where('classroom_id', $user_class->id)
            ->first();

        $has_masuk = false;

        if ($today_summary) {
            $existing_types = StudentAttendance::where('summary_id', $today_summary->id)
                ->pluck('type')
                ->unique()
                ->toArray();

            $has_masuk = in_array(SessionType::MASUK, $existing_types);
        }

        return view('dashboard.student-attendance.index', [
            'title' => 'Absen Kehadiran Siswa',
            'session_types' => SessionType::options(),
            'has_masuk' => $has_masuk,
        ]);
    }

    public function create(SessionType $session_type)
    {
        $user_class = auth()->user()->classrooms()->first();
        $today_attendance = StudentAttendanceSummary::whereToday('created_at')->where('classroom_id', $user_class->id)->first();
        $old_attendance = null;

        if ($today_attendance) {
            $today_attendance = StudentAttendanceSummary::whereToday('created_at')->where('classroom_id', $user_class->id)->first();
            $old_attendance = StudentAttendance::where('summary_id', $today_attendance->id)->where('type', $session_type)->get()->keyBy('class_member_id');
        }

        $members = ClassMember::where('classroom_id', $user_class->id)
            ->whereNot('role', ClassRole::WALI_KELAS)
            ->get();

        return view('dashboard.student-attendance.form', [
            'title' => 'Absen Siswa - ' . $user_class->name,
            'statuss' => AttendanceType::options(),
            'members' => $members,
            'classroom' => $user_class,
            'old_attendance' => $old_attendance,
            'old_summary_id' => $today_attendance?->id,
            'session_type' => $session_type
        ]);
    }

    public function store(Request $request, SessionType $session_type)
    {
        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.status' => 'required|string',
            'attendances.*.note' => 'nullable|string',
        ]);

        $classroom_id = auth()->user()->classrooms()->first()?->id;

        $today_summary = StudentAttendanceSummary::whereDate('created_at', today())
            ->where('classroom_id', $classroom_id)
            ->first();

        if ($today_summary) {
            $existing_attendance = StudentAttendance::where('summary_id', $today_summary->id)
                ->where('type', $session_type)
                ->exists();

            if ($existing_attendance) {
                return redirect()->back()
                    ->with('error', 'Absensi ' . $session_type->value . ' sudah dilakukan sebelumnya');
            }
        }

        $counts = $this->calculateAttendanceCounts($validated['attendances'], $session_type, $classroom_id);
        $status = $this->determineAttendanceStatus($session_type, $classroom_id);

        DB::transaction(function () use ($validated, $counts, $session_type, $status, $classroom_id, $today_summary) {
            $summary = $today_summary ?? StudentAttendanceSummary::create([
                'classroom_id' => $classroom_id,
                'author_id' => auth()->id(),
                'created_at' => now(),
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alpa' => 0,
                'pkl' => 0,
                'status' => $status,
            ]);

            $summary->update([
                'hadir' => $counts['hadir'],
                'izin' => $counts['izin'],
                'sakit' => $counts['sakit'],
                'alpa' => $counts['alpa'],
                'pkl' => $counts['pkl'],
                'status' => $status,
            ]);

            foreach ($validated['attendances'] as $memberId => $attendance) {
                StudentAttendance::create([
                    'summary_id' => $summary->id,
                    'class_member_id' => $memberId,
                    'status' => $attendance['status'],
                    'note' => $attendance['note'] ?? null,
                    'type' => $session_type,
                ]);
            }
        });

        return redirect()->route('student-attendance.index')->with('success', 'Absensi berhasil disimpan.');
    }

    private function calculateAttendanceCounts(array $attendances, SessionType $current_session_type, string $classroom_id): array
    {
        $counts = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'pkl' => 0,
        ];

        $today_summary = StudentAttendanceSummary::whereDate('created_at', today())
            ->where('classroom_id', $classroom_id)
            ->first();

        foreach ($attendances as $memberId => $attendance) {
            $current_status = strtolower($attendance['status']);
            $opposite_status = null;

            if ($today_summary) {
                $opposite_attendance = StudentAttendance::where('summary_id', $today_summary->id)
                    ->where('class_member_id', $memberId)
                    ->where('type', $current_session_type->opposite())
                    ->first();

                $opposite_status = $opposite_attendance ? $opposite_attendance->status->value : null;
            }

            if ($opposite_status) {
                if ($current_status === 'hadir' && $opposite_status === 'hadir') {
                    $counts['hadir']++;
                } elseif ($current_status !== 'hadir' || $opposite_status !== 'hadir') {
                    $status_to_count = $current_status !== 'hadir' ? $current_status : $opposite_status;
                    $counts[$status_to_count]++;
                }
            } else {
                if (array_key_exists($current_status, $counts)) {
                    $counts[$current_status]++;
                }
            }
        }

        return $counts;
    }

    private function determineAttendanceStatus(SessionType $session_type, string $classroom_id): StudentAttendanceStatus
    {
        $today_summary = StudentAttendanceSummary::whereDate('created_at', today())
            ->where('classroom_id', $classroom_id)
            ->first();

        if (!$today_summary) {
            return $session_type === SessionType::MASUK
                ? StudentAttendanceStatus::BELUM_ABSEN_KELUAR
                : StudentAttendanceStatus::BELUM_ABSEN_MASUK;
        }

        $existing_types = StudentAttendance::where('summary_id', $today_summary->id)
            ->pluck('type')
            ->unique()
            ->toArray();

        if (in_array($session_type->opposite(), $existing_types)) {
            return StudentAttendanceStatus::SUDAH_LENGKAP;
        }

        return $session_type === SessionType::MASUK
            ? StudentAttendanceStatus::BELUM_ABSEN_KELUAR
            : StudentAttendanceStatus::BELUM_ABSEN_MASUK;
    }

    public function update(Request $request, StudentAttendanceSummary $summary, SessionType $session_type)
    {
        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.status' => 'required|string',
            'attendances.*.note' => 'nullable|string',
        ]);

        $counts = $this->calculateAttendanceCounts($validated['attendances'], $session_type, $summary->classroom_id);

        DB::transaction(function () use ($validated, $summary, $counts, $session_type) {
            $summary->update([
                'hadir' => $counts['hadir'],
                'izin' => $counts['izin'],
                'sakit' => $counts['sakit'],
                'alpa' => $counts['alpa'],
                'pkl' => $counts['pkl'],
                'status' => $this->determineAttendanceStatus($session_type, $summary->classroom_id),
            ]);

            foreach ($validated['attendances'] as $memberId => $attendance) {
                StudentAttendance::where('summary_id', $summary->id)
                    ->where('class_member_id', $memberId)
                    ->where('type', $session_type)
                    ->update([
                        'status' => $attendance['status'],
                        'note' => $attendance['note'] ?? null,
                    ]);
            }
        });

        return redirect()->route('student-attendance.index')->with('success', 'Absensi berhasil diperbarui.');
    }
}
