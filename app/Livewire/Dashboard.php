<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Classes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $todayAttendance = [];
    public $recentSessions = [];
    public $attendanceStats = [];
    public $lastUpdated;
    public $todaySchedules = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $this->lastUpdated = now()->format('d-m-Y H:i:s');

        // todayAttendance: ambil semua absensi hari ini milik user
        $this->todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('checked_at', $today)
            ->get();

        // Semua jadwal hari ini + status absensi (guru & ketua kelas)
        $this->todaySchedules = $this->getTodaySchedulesWithStatus($user, $today);

        if ($user->isAdmin()) {
            $this->loadAdminStats($today);
        } elseif ($user->isGuru()) {
            $this->loadGuruStats($user, $today);
        } else {
            $this->loadSiswaStats($user, $today);
        }
    }

    private function loadAdminStats($today)
    {
        // Cache these counts as they don't change often
        $this->attendanceStats = [
            'total_classes' => cache()->remember('total_active_classes', now()->addHours(6), function () {
                return Classes::where('is_active', true)->count();
            }),
            'active_users' => cache()->remember('total_active_users', now()->addHours(6), function () {
                return User::where('is_active', true)->count();
            }),
            'today_attendance_count' => Attendance::whereDate('checked_at', $today)->count(),
            'total_absensi_lengkap' => $this->getTotalAbsensiLengkap($today),
        ];

        // Optimized recent sessions query
        $this->recentSessions = Attendance::query()
            ->selectRaw('class_id, subject_id, checked_at, MAX(updated_at) as last_update')
            ->groupBy('class_id', 'subject_id', 'checked_at')
            ->orderByDesc('last_update')
            ->limit(5)
            ->get();
    }

    private function loadGuruStats($user, $today)
    {
        $this->attendanceStats = [
            'my_classes' => $user->classes()->where('classes.is_active', true)->count(),
            'today_attendance_count' => $user->teachingSubjects()->count(),
            'total_absensi_lengkap' => $this->getTotalAbsensiLengkap($today, $user->id),
        ];
    }

    private function loadSiswaStats($user, $today)
    {
        if ($user->isKetuaKelas()) {
            $kelas = $user->activeClass('ketua_kelas');
            $this->todaySchedules = $this->getTodaySchedulesWithStatus($user, $today);
            $sesiLengkap = collect($this->todaySchedules)->where(function($jadwal) {
                return $jadwal['absen_masuk'] == $jadwal['total_siswa'] && $jadwal['absen_keluar'] == $jadwal['total_siswa'];
            })->count();
            $totalSesi = count($this->todaySchedules);
            $this->attendanceStats = [
                'jumlah_siswa_kelas' => $kelas ? $kelas->members()
                    ->wherePivot('role', 'siswa')
                    ->wherePivot('is_active', 1)
                    ->count() : 0,
                'jumlah_guru_kelas' => $kelas ? $kelas->members()
                    ->wherePivot('role', 'guru')
                    ->wherePivot('is_active', 1)
                    ->count() : 0,
                'nama_kelas' => $kelas?->name ?? '-',
                'today_attendance_count' => $kelas ? $kelas->subjects()->count() : 0,
                'total_absensi_lengkap' => $totalSesi > 0 ? ("$sesiLengkap/$totalSesi") : '0/0',
            ];
        } else {
            $this->attendanceStats = [
                // Ganti dari jumlah kelas menjadi jumlah total absen milik siswa
                'total_absen_saya' => Attendance::where('user_id', $user->id)->count(),
                'today_attendance_count' => $user->classes()->with('subjects')->get()->sum(function($class) {
                    return $class->subjects->count();
                }),
                'total_absensi_lengkap' => $this->getTotalAbsensiLengkap($today, $user->id),
            ];
        }
    }

    private function getTotalAbsensiLengkap($today, $userId = null)
    {
        $query = Attendance::whereDate('checked_at', $today);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        // Hitung absensi yang sudah lengkap (masuk dan keluar)
        $absensiLengkap = $query->select('checked_at', 'class_id', 'subject_id')
            ->groupBy('checked_at', 'class_id', 'subject_id')
            ->havingRaw('COUNT(*) >= 2') // Minimal 2 record (masuk + keluar)
            ->count();
            
        return $absensiLengkap;
    }

    private function getTodaySchedulesWithStatus($user, $today)
    {
        $result = [];
        $hari = Carbon::parse($today)->locale('id')->isoFormat('dddd');
        $hari = ucfirst(strtolower($hari));
        if ($user->isAdmin()) {
            // Semua kelas aktif
            $kelasList = Classes::where('is_active', true)->get();
            foreach ($kelasList as $kelas) {
                $schedules = $kelas->schedules()->where('day', $hari)->with('subject')->get();
                foreach ($schedules as $schedule) {
                    $subject = $schedule->subject;
                    $siswa = $kelas->members()
                        ->wherePivotIn('role', ['siswa', 'ketua_kelas'])
                        ->wherePivot('is_active', true)
                        ->get();
                    $siswaIds = $siswa->pluck('id')->toArray();
                    $absenMasuk = Attendance::where('class_id', $kelas->id)
                        ->where('subject_id', $subject->id)
                        ->whereDate('checked_at', $today)
                        ->whereIn('user_id', $siswaIds)
                        ->where('session_type', 'masuk')
                        ->pluck('user_id')->unique()->count();
                    $absenKeluar = Attendance::where('class_id', $kelas->id)
                        ->where('subject_id', $subject->id)
                        ->whereDate('checked_at', $today)
                        ->whereIn('user_id', $siswaIds)
                        ->where('session_type', 'keluar')
                        ->pluck('user_id')->unique()->count();
                    if ($absenMasuk == 0 && $absenKeluar == 0) {
                        $status = 'belum_absen';
                    } elseif ($absenMasuk == 0 && $absenKeluar == count($siswaIds)) {
                        $status = 'belum_absen_masuk';
                    } elseif ($absenMasuk == count($siswaIds) && $absenKeluar == 0) {
                        $status = 'belum_absen_keluar';
                    } elseif ($absenMasuk == count($siswaIds) && $absenKeluar == count($siswaIds)) {
                        $status = 'lengkap';
                    } else {
                        $status = 'belum_lengkap';
                    }
                    $result[] = [
                        'kelas' => $kelas->name,
                        'mapel' => $subject->name,
                        'guru' => $schedule->user ? $schedule->user->name : '-',
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'absen_masuk' => $absenMasuk,
                        'absen_keluar' => $absenKeluar,
                        'total_siswa' => count($siswaIds),
                        'status' => $status,
                    ];
                }
            }
        } elseif ($user->isGuru()) {
            $kelasList = $user->classes()->where('classes.is_active', true)->get();
            foreach ($kelasList as $kelas) {
                $schedules = $kelas->schedules()->where('day', $hari)->with('subject')->get();
                foreach ($schedules as $schedule) {
                    $subject = $schedule->subject;
                    $siswa = $kelas->members()
                        ->wherePivotIn('role', ['siswa', 'ketua_kelas'])
                        ->wherePivot('is_active', true)
                        ->get();
                    $siswaIds = $siswa->pluck('id')->toArray();
                    $absenMasuk = Attendance::where('class_id', $kelas->id)
                        ->where('subject_id', $subject->id)
                        ->whereDate('checked_at', $today)
                        ->whereIn('user_id', $siswaIds)
                        ->where('session_type', 'masuk')
                        ->pluck('user_id')->unique()->count();
                    $absenKeluar = Attendance::where('class_id', $kelas->id)
                        ->where('subject_id', $subject->id)
                        ->whereDate('checked_at', $today)
                        ->whereIn('user_id', $siswaIds)
                        ->where('session_type', 'keluar')
                        ->pluck('user_id')->unique()->count();
                    if ($absenMasuk == 0 && $absenKeluar == 0) {
                        $status = 'belum_absen';
                    } elseif ($absenMasuk == 0 && $absenKeluar == count($siswaIds)) {
                        $status = 'belum_absen_masuk';
                    } elseif ($absenMasuk == count($siswaIds) && $absenKeluar == 0) {
                        $status = 'belum_absen_keluar';
                    } elseif ($absenMasuk == count($siswaIds) && $absenKeluar == count($siswaIds)) {
                        $status = 'lengkap';
                    } else {
                        $status = 'belum_lengkap';
                    }
                    $result[] = [
                        'kelas' => $kelas->name,
                        'mapel' => $subject->name,
                        'guru' => $schedule->user ? $schedule->user->name : '-',
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'absen_masuk' => $absenMasuk,
                        'absen_keluar' => $absenKeluar,
                        'total_siswa' => count($siswaIds),
                        'status' => $status,
                    ];
                }
            }
        } elseif ($user->isKetuaKelas()) {
            $kelas = $user->activeClass('ketua_kelas');
            if ($kelas) {
                $schedules = $kelas->schedules()->where('day', $hari)->with('subject')->get();
                foreach ($schedules as $schedule) {
                    $subject = $schedule->subject;
                    $siswa = $kelas->members()
                        ->wherePivotIn('role', ['siswa', 'ketua_kelas'])
                        ->wherePivot('is_active', true)
                        ->get();
                    $siswaIds = $siswa->pluck('id')->toArray();
                    $absenMasuk = Attendance::where('class_id', $kelas->id)
                        ->where('subject_id', $subject->id)
                        ->whereDate('checked_at', $today)
                        ->whereIn('user_id', $siswaIds)
                        ->where('session_type', 'masuk')
                        ->pluck('user_id')->unique()->count();
                    $absenKeluar = Attendance::where('class_id', $kelas->id)
                        ->where('subject_id', $subject->id)
                        ->whereDate('checked_at', $today)
                        ->whereIn('user_id', $siswaIds)
                        ->where('session_type', 'keluar')
                        ->pluck('user_id')->unique()->count();
                    $status = 'belum_absen';
                    if ($absenMasuk == 0 && $absenKeluar == 0) {
                        $status = 'belum_absen';
                    } elseif ($absenMasuk == 0 && $absenKeluar == count($siswaIds)) {
                        $status = 'belum_absen_masuk';
                    } elseif ($absenMasuk == count($siswaIds) && $absenKeluar == 0) {
                        $status = 'belum_absen_keluar';
                    } elseif ($absenMasuk == count($siswaIds) && $absenKeluar == count($siswaIds)) {
                        $status = 'lengkap';
                    } else {
                        $status = 'belum_lengkap';
                    }
                    $result[] = [
                        'kelas' => $kelas->name,
                        'mapel' => $subject->name,
                        'guru' => $schedule->user ? $schedule->user->name : '-',
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'absen_masuk' => $absenMasuk,
                        'absen_keluar' => $absenKeluar,
                        'total_siswa' => count($siswaIds),
                        'status' => $status,
                    ];
                }
            }
        } else {
            // Siswa: tampilkan jadwal kelas hari ini
            $kelasList = $user->classes()->where('classes.is_active', true)->get();
            foreach ($kelasList as $kelas) {
                $schedules = $kelas->schedules()->where('day', $hari)->with('subject')->get();
                foreach ($schedules as $schedule) {
                    $subject = $schedule->subject;
                    $siswa = $kelas->members()
                        ->wherePivotIn('role', ['siswa', 'ketua_kelas'])
                        ->wherePivot('is_active', true)
                        ->get();
                    $siswaIds = $siswa->pluck('id')->toArray();
                    $absenMasuk = Attendance::where('class_id', $kelas->id)
                        ->where('subject_id', $subject->id)
                        ->whereDate('checked_at', $today)
                        ->whereIn('user_id', $siswaIds)
                        ->where('session_type', 'masuk')
                        ->pluck('user_id')->unique()->count();
                    $absenKeluar = Attendance::where('class_id', $kelas->id)
                        ->where('subject_id', $subject->id)
                        ->whereDate('checked_at', $today)
                        ->whereIn('user_id', $siswaIds)
                        ->where('session_type', 'keluar')
                        ->pluck('user_id')->unique()->count();
                    $status = 'belum_absen';
                    if ($absenMasuk == 0 && $absenKeluar == 0) {
                        $status = 'belum_absen';
                    } elseif ($absenMasuk == 0 && $absenKeluar == count($siswaIds)) {
                        $status = 'belum_absen_masuk';
                    } elseif ($absenMasuk == count($siswaIds) && $absenKeluar == 0) {
                        $status = 'belum_absen_keluar';
                    } elseif ($absenMasuk == count($siswaIds) && $absenKeluar == count($siswaIds)) {
                        $status = 'lengkap';
                    } else {
                        $status = 'belum_lengkap';
                    }
                    $result[] = [
                        'kelas' => $kelas->name,
                        'mapel' => $subject->name,
                        'guru' => $schedule->user ? $schedule->user->name : '-',
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'absen_masuk' => $absenMasuk,
                        'absen_keluar' => $absenKeluar,
                        'total_siswa' => count($siswaIds),
                        'status' => $status,
                    ];
                }
            }
        }
        return $result;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}