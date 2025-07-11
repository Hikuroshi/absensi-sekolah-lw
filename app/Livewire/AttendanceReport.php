<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Classes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\AttendanceSummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class AttendanceReport extends Component
{
    use WithPagination;

    protected $queryString = ['searchStudent', 'selectedClass', 'dateFrom', 'dateTo'];

    public $selectedClass;
    public $dateFrom;
    public $dateTo;
    public $searchStudent = '';

    public function mount()
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function updatedSelectedClass()
    {
        $this->resetPage();
        $this->searchStudent = '';
        $this->dispatch('reset-student-search');
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedSearchStudent()
    {
        $this->resetPage();
    }

    // Export absensi efisien tanpa queue, dengan filter dan pembatasan data
    public function exportReport(Request $request)
    {
        $dateFrom = $request->input('dateFrom', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('dateTo', Carbon::now()->format('Y-m-d'));
        $selectedClass = $request->input('selectedClass');
        $searchStudent = $request->input('searchStudent', '');

        $maxRows = 10000;

        $query = Attendance::query()
            ->select([
                'user_id',
                'class_id',
                'subject_id',
                \DB::raw('MAX(checked_at) as checked_at'),
                \DB::raw('SUM(status = "hadir" AND session_type = "masuk") as hadir_masuk'),
                \DB::raw('SUM(status = "hadir" AND session_type = "keluar") as hadir_keluar'),
                \DB::raw('SUM(status = "izin" AND session_type = "masuk") as izin_masuk'),
                \DB::raw('SUM(status = "izin" AND session_type = "keluar") as izin_keluar'),
                \DB::raw('SUM(status = "sakit" AND session_type = "masuk") as sakit_masuk'),
                \DB::raw('SUM(status = "sakit" AND session_type = "keluar") as sakit_keluar'),
                \DB::raw('SUM(status = "alpa" AND session_type = "masuk") as alpa_masuk'),
                \DB::raw('SUM(status = "alpa" AND session_type = "keluar") as alpa_keluar'),
            ])
            ->whereBetween('checked_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay()
            ]);
        if ($selectedClass) {
            $query->where('class_id', $selectedClass);
        }
        if (!empty($searchStudent)) {
            $query->whereHas('user', function($q) use ($searchStudent) {
                $q->where('name', 'like', '%' . $searchStudent . '%');
            });
        }
        $query = $query->groupBy('user_id', 'class_id', 'subject_id');
        $count = $query->count();
        if ($count > $maxRows) {
            return back()->with('message', 'Data terlalu banyak untuk diekspor. Silakan persempit filter.');
        }
        $export = new AttendanceSummaryExport($dateFrom, $dateTo, $selectedClass, null, $searchStudent);
        $filename = 'laporan-absensi-' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download($export, $filename);
    }

    public function render()
    {
        $user = Auth::user();
        
        // Query untuk available classes
        $availableClasses = $this->getAvailableClasses($user);
        
        // Query untuk attendance data
        $attendanceData = $this->getAttendanceData($user);
        
        // Query untuk available users
        $availableUsers = $this->getAvailableUsers();
        
        return view('livewire.attendance-report', [
            'attendanceData' => $attendanceData,
            'availableClasses' => $availableClasses,
            'availableUsers' => $availableUsers,
        ]);
    }
    
    protected function getAvailableClasses($user)
    {
        if ($user->isAdmin()) {
            return Classes::where('is_active', true)->get();
        } 
        
        if ($user->isGuru()) {
            return $user->classes()->where('classes.is_active', true)->get();
        }
        
        if ($user->isKetuaKelas()) {
            $kelas = $user->activeClass('ketua_kelas');
            return $kelas ? collect([$kelas]) : collect();
        }
        
        return collect();
    }
    
    protected function getAttendanceData($user)
    {
        $query = Attendance::query()
            ->select([
                'user_id',
                'class_id',
                'subject_id',
                \DB::raw('MAX(checked_at) as checked_at'),
                \DB::raw('SUM(status = "hadir" AND session_type = "masuk") as hadir_masuk'),
                \DB::raw('SUM(status = "hadir" AND session_type = "keluar") as hadir_keluar'),
                \DB::raw('SUM(status = "izin" AND session_type = "masuk") as izin_masuk'),
                \DB::raw('SUM(status = "izin" AND session_type = "keluar") as izin_keluar'),
                \DB::raw('SUM(status = "sakit" AND session_type = "masuk") as sakit_masuk'),
                \DB::raw('SUM(status = "sakit" AND session_type = "keluar") as sakit_keluar'),
                \DB::raw('SUM(status = "alpa" AND session_type = "masuk") as alpa_masuk'),
                \DB::raw('SUM(status = "alpa" AND session_type = "keluar") as alpa_keluar'),
            ])
            ->whereBetween('checked_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay()
            ]);
        // Filter berdasarkan role user
        if ($user->isSiswa()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isGuru() && !$user->isAdmin()) {
            $guruClasses = $user->classes()->pluck('classes.id');
            $query->whereIn('class_id', $guruClasses);
        } elseif ($user->isKetuaKelas()) {
            $kelas = $user->activeClass('ketua_kelas');
            if ($kelas) {
                $query->where('class_id', $kelas->id);
            }
        }
        // Filter tambahan
        if ($this->selectedClass && !$user->isSiswa()) {
            $query->where('class_id', $this->selectedClass);
        }
        if (!empty($this->searchStudent) && !$user->isSiswa()) {
            $search = $this->searchStudent;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        $query = $query->groupBy('user_id', 'class_id', 'subject_id')
            ->with([
                'user:id,name', // hanya ambil kolom yang diperlukan
                'class:id,name',
                'subject:id,name',
            ])
            ->orderBy('user_id');
        // Untuk data besar, gunakan simplePaginate
        return $query->simplePaginate(10);
    }
    
    protected function getAvailableUsers()
    {
        if (!$this->selectedClass) {
            return collect();
        }
        
        return Classes::find($this->selectedClass)
            ?->members()
                    ->wherePivotIn('role', ['siswa', 'ketua_kelas'])
                    ->wherePivot('is_active', true)
                    ->orderBy('name')
            ->get() ?? collect();
    }
}
