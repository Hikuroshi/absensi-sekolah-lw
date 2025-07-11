<?php

use App\Livewire\AttendanceRecord;
use App\Livewire\AttendanceReport;
use App\Livewire\ClassManagement;
use App\Livewire\ClassDetail;
use App\Livewire\Dashboard;
use App\Livewire\Login;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Exports\AttendanceSummaryExport;
use Maatwebsite\Excel\Facades\Excel;

Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/attendance-record', AttendanceRecord::class)->name('attendance.record');
    Route::get('/attendance-report', AttendanceReport::class)->name('attendance.report');
    Route::get('/class-management', ClassManagement::class)->name('class.management');
    Route::get('/class/{id}', ClassDetail::class)->name('class.detail');
});

Route::middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/user-management', \App\Livewire\UserManagement::class)->name('user.management');
    Route::get('/subject-management', \App\Livewire\SubjectManagement::class)->name('subject.management');
    Route::get('/schedule-management', \App\Livewire\ScheduleManagement::class)->name('schedule.management');
});

Route::get('/login', Login::class)->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::get('/attendance-report/export', function () {
    $dateFrom = request('dateFrom');
    $dateTo = request('dateTo');
    $classId = request('selectedClass');
    $userId = request('selectedUser');

    return Excel::download(
        new AttendanceSummaryExport($dateFrom, $dateTo, $classId, $userId),
        'laporan-absensi.xlsx'
    );
})->middleware('auth')->name('attendance.report.export');
