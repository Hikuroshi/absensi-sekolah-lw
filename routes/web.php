<?php

use App\Livewire\AttendanceRecord;
use App\Livewire\AttendanceReport;
use App\Livewire\ClassManagement;
use App\Livewire\ClassDetail;
use App\Livewire\Dashboard;
use App\Livewire\Login;
use App\Livewire\ScheduleManagement;
use App\Livewire\SubjectManagement;
use App\Livewire\UserManagement;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Exports\AttendanceSummaryExport;
use Maatwebsite\Excel\Facades\Excel;

Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/attendance-record', AttendanceRecord::class)->name('attendance.record');
    Route::get('/attendance-report', AttendanceReport::class)->name('attendance.report');
    Route::get('/attendance-report/export', [AttendanceReport::class, 'exportReport'])->name('attendance.report.export');
    Route::get('/class-management', ClassManagement::class)->name('class.management');
    Route::get('/class/{id}', ClassDetail::class)->name('class.detail');
});

Route::middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/user-management', UserManagement::class)->name('user.management');
    Route::get('/subject-management', SubjectManagement::class)->name('subject.management');
    Route::get('/schedule-management', ScheduleManagement::class)->name('schedule.management');
});

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');