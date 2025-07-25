<?php

namespace App\Http\Controllers;

use App\Models\StudentAttendance;
use App\Models\StudentAttendanceSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PageController extends Controller
{
    public function dashboard()
    {
        $type = request('type', 'today');
        $user = auth()->user();

        $summaryQuery = StudentAttendanceSummary::query();
        $attendanceQuery = StudentAttendance::query();

        if (Gate::allows('isKetuaKelas') || Gate::allows('isWaliKelas')) {
            $classroomId = $user->classrooms()->first()?->id;
            $summaryQuery->where('classroom_id', $classroomId);
            $attendanceQuery->whereHas('summary', fn($q) => $q->where('classroom_id', $classroomId));
        } elseif (Gate::allows('isSiswa')) {
            $attendanceQuery->where('class_member_id', $user->id);
            $summaryIds = $attendanceQuery->pluck('summary_id');
            $summaryQuery->whereIn('id', $summaryIds);
        }

        if ($type !== 'all') {
            $summaryQuery = $this->applyDateFilter($summaryQuery, $type);
            $attendanceQuery = $this->applyDateFilter($attendanceQuery, $type);
        }

        $summaryData = $summaryQuery->get();
        $todaySummary = $summaryQuery->clone()->whereDate('created_at', today())->first();

        $totals = [
            'hadir' => $summaryData->sum('hadir'),
            'izin' => $summaryData->sum('izin'),
            'sakit' => $summaryData->sum('sakit'),
            'alpa' => $summaryData->sum('alpa'),
            'pkl' => $summaryData->sum('pkl'),
        ];

        return view('dashboard.index', [
            'title' => 'Dashboard',
            'todaySummary' => $todaySummary,
            'totalSummary' => $totals,
            'type' => $type
        ]);
    }

    protected function applyDateFilter($query, $type, $dateColumn = 'created_at')
    {
        return $query->when($type === 'today', function ($q) use ($dateColumn) {
            $q->whereDate($dateColumn, today());
        })
            ->when($type === 'week', function ($q) use ($dateColumn) {
                $q->whereBetween($dateColumn, [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
            })
            ->when($type === 'month', function ($q) use ($dateColumn) {
                $q->whereBetween($dateColumn, [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ]);
            })
            ->when($type === 'year', function ($q) use ($dateColumn) {
                $q->whereBetween($dateColumn, [
                    now()->startOfYear(),
                    now()->endOfYear()
                ]);
            });
    }
}
