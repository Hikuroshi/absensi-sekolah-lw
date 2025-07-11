<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Classes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AttendanceRecord extends Component
{
    public $selectedClass;
    public $classMembers = [];
    public $attendanceData = [];
    public $selectedSubject;
    public $availableSubjects = [];
    public $isAbsensiStarted = false;
    public $isAbsensiFinished = false;
    public $sessionType = 'masuk';
    public $checkedAt = null;
    
    protected $listeners = ['resetAbsensi' => 'resetAbsensi'];

    public function mount()
    {
        $user = Auth::user();
        if ($user->isKetuaKelas()) {
            $this->selectedClass = $user->activeClass('ketua_kelas')->id ?? null;
            $this->loadSubjects();
            if (count($this->availableSubjects) > 0) {
                $this->selectedSubject = $this->availableSubjects->first()->id;
                $this->loadClassMembers();
            }
        }
    }

    public function startAbsensi()
    {
        $this->isAbsensiStarted = true;
        $this->isAbsensiFinished = false;
        $this->checkedAt = Carbon::now();
        $this->loadClassMembers();
        $this->loadAttendanceData();
    }

    public function finishAbsensi()
    {
        // Cek apakah semua anggota sudah diabsen
        $notMarked = collect($this->classMembers)->filter(function($member) {
            $id = is_object($member) ? $member->id : (is_array($member) ? $member['id'] : $member);
            return !isset($this->attendanceData[$id]);
        });
        if ($notMarked->count() > 0) {
            session()->flash('error', 'Masih ada anggota yang belum diabsen!');
            return;
        }
        $this->isAbsensiFinished = true;
        $this->isAbsensiStarted = false;
        $this->checkedAt = null;
        session()->flash('message', 'Absensi berhasil disimpan!');
    }

    public function resetAbsensi()
    {
        $this->isAbsensiStarted = false;
        $this->isAbsensiFinished = false;
        $this->attendanceData = [];
        $this->classMembers = [];
        $this->checkedAt = null;
    }

    public function selectClass($classId)
    {
        $this->selectedClass = $classId;
        $this->selectedSubject = null;
        $this->availableSubjects = [];
        $this->classMembers = [];
        $this->isAbsensiStarted = false;
        $this->loadSubjects();
    }

    public function loadSubjects()
    {
        if (!$this->selectedClass) return;
        $today = now()->locale('id')->isoFormat('dddd');
        // Ubah hari ke format kapital awal (Senin, Selasa, dst)
        $today = ucfirst(strtolower($today));
        $schedules = \App\Models\Schedule::where('class_id', $this->selectedClass)
            ->where('day', $today)
            ->with('subject')
            ->get();
        $this->availableSubjects = $schedules->pluck('subject')->unique('id')->values();
    }

    public function selectSubject($subjectId)
    {
        $this->selectedSubject = $subjectId;
        $this->isAbsensiStarted = false;
        $this->loadClassMembers();
    }

    public function loadClassMembers()
    {
        if (!$this->selectedClass || !$this->selectedSubject) {
            $this->classMembers = [];
            return;
        }
        
        $class = Classes::find($this->selectedClass);
        $subject = \App\Models\Subject::find($this->selectedSubject);
        
        $siswa = $class ? $class->members()
            ->wherePivotIn('role', ['siswa', 'ketua_kelas'])
            ->wherePivot('is_active', true)
            ->orderBy('name')
            ->get() : collect();
        
        $guru = \App\Models\User::whereHas('teachingAssignments', function($q) {
            $q->where('class_id', $this->selectedClass)
            ->where('subject_id', $this->selectedSubject);
        })->get();
        
        $today = \Carbon\Carbon::today();
        $attendanceUserIds = \App\Models\Attendance::where('class_id', $this->selectedClass)
            ->where('subject_id', $this->selectedSubject)
            ->where('session_type', $this->sessionType)
            ->whereDate('checked_at', $today)
            ->pluck('user_id')
            ->toArray();
        
        $allMembers = $siswa->concat($guru);
        
        if (!empty($attendanceUserIds)) {
            $extraUsers = \App\Models\User::whereIn('id', $attendanceUserIds)
                ->whereNotIn('id', $allMembers->pluck('id'))
                ->get();
            $allMembers = $allMembers->concat($extraUsers);
        }

        // Urutkan: guru, ketua_kelas, siswa
        $this->classMembers = $allMembers->unique('id')->sortBy(function($member) {
            $role = $member->pivot->role ?? $member->role ?? '';
            if ($role === 'guru') return 0;
            if ($role === 'ketua_kelas') return 1;
            return 2; // siswa atau lainnya
        })->values();
    }

    public function loadAttendanceData()
    {
        $today = \Carbon\Carbon::today();
        $attendances = Attendance::where('class_id', $this->selectedClass)
            ->where('subject_id', $this->selectedSubject)
            ->where('session_type', $this->sessionType)
            ->whereDate('checked_at', $today)
            ->with('user')
            ->get();
        
        // Pastikan attendanceData adalah array yang dapat diakses dengan mudah
        $this->attendanceData = [];
        foreach ($attendances as $attendance) {
            $this->attendanceData[$attendance->user_id] = [
                'id' => $attendance->id,
                'status' => $attendance->status,
                'checked_at' => $attendance->checked_at,
                'user_id' => $attendance->user_id,
            ];
        }
    }

    public function updateAttendance($userId, $status)
    {
        $today = \Carbon\Carbon::today();
        $attendance = Attendance::where('class_id', $this->selectedClass)
            ->where('subject_id', $this->selectedSubject)
            ->where('session_type', $this->sessionType)
            ->whereDate('checked_at', $today)
            ->where('user_id', $userId)
            ->first();

        $checkedAt = $this->checkedAt ?? Carbon::now();

        if ($attendance) {
            $attendance->update([
                'status' => $status,
                'checked_at' => $checkedAt,
            ]);
        } else {
            $attendance = Attendance::create([
                'class_id' => $this->selectedClass,
                'subject_id' => $this->selectedSubject,
                'session_type' => $this->sessionType,
                'user_id' => $userId,
                'status' => $status,
                'checked_at' => $checkedAt,
            ]);
        }
        
        // Update attendanceData langsung untuk immediate feedback
        $this->attendanceData[$userId] = [
            'id' => $attendance->id,
            'status' => $status,
            'checked_at' => $attendance->checked_at,
            'user_id' => $userId,
        ];
        // Tambahkan ini agar data anggota kelas (beserta pivot) selalu fresh
        $this->loadClassMembers();
    }

    // Helper method untuk mendapatkan status attendance
    public function getAttendanceStatus($userId)
    {
        return isset($this->attendanceData[$userId]) ? $this->attendanceData[$userId]['status'] : null;
    }

    // Helper method untuk mengecek apakah button harus aktif
    public function isButtonActive($userId, $status)
    {
        return $this->getAttendanceStatus($userId) === $status;
    }

    public function render()
    {
        $user = Auth::user();
        $availableClasses = collect();
        
        if ($user->isAdmin()) {
            $availableClasses = Classes::where('is_active', true)->get();
        } elseif ($user->isKetuaKelas()) {
            $kelas = $user->activeClass('ketua_kelas');
            $availableClasses = $kelas ? collect([$kelas]) : collect();
        }
        
        return view('livewire.attendance-record', [
            'availableClasses' => $availableClasses,
            'availableSubjects' => $this->availableSubjects,
            'selectedSubject' => $this->selectedSubject,
            'sessionType' => $this->sessionType,
        ]);
    }
}