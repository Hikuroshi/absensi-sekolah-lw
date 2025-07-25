<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassMemberController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\Imports\ScheduleImportController;
use App\Http\Controllers\Imports\StudentClassImportController;
use App\Http\Controllers\Imports\SubjectImportController;
use App\Http\Controllers\Imports\TeacherImportController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\SubjectAttendanceController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'authenticate')->name('authenticate');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::controller(PageController::class)->group(function () {
        Route::get('/', 'dashboard')->name('dashboard');
    });

    Route::middleware('can:isAdmin')->group(function () {
        Route::resource('user', UserController::class)->except(['show']);
        Route::patch('/user/{user}/password', [UserController::class, 'updatePassword'])->name('user.update-password');

        Route::resource('classroom', ClassroomController::class);
        Route::resource('subject', SubjectController::class)->except(['show']);
        Route::resource('schedule', ScheduleController::class)->except(['show']);

        Route::controller(ClassMemberController::class)->group(function () {
            Route::put('/class-member/{classroom}', 'leaderUpdate')->name('class-member.leader-update');
            Route::get('/class-member/{classroom}/create-student', 'createStudent')->name('class-member.create-student');
            Route::post('/class-member/{classroom}/store-student', 'storeStudent')->name('class-member.store-student');
            Route::delete('/class-member/{classroom}/delete-student/{student}', 'deleteStudent')->name('class-member.delete-student');
        });

        Route::controller(SubjectImportController::class)->group(function () {
            Route::get('/import-subject', 'index')->name('import-subject.index');
            Route::post('/import-subject', 'import')->name('import-subject.store');
        });

        Route::controller(TeacherImportController::class)->group(function () {
            Route::get('/import-teacher', 'index')->name('import-teacher.index');
            Route::post('/import-teacher', 'import')->name('import-teacher.store');
        });

        Route::controller(StudentClassImportController::class)->group(function () {
            Route::get('/import-student-class', 'index')->name('import-student-class.index');
            Route::post('/import-student-class', 'import')->name('import-student-class.store');
        });

        Route::controller(ScheduleImportController::class)->group(function () {
            Route::get('/import-schedule', 'index')->name('import-schedule.index');
            Route::post('/import-schedule', 'import')->name('import-schedule.store');
        });
    });

    Route::middleware('can:isKetuaKelas')->group(function () {
        Route::controller(StudentAttendanceController::class)->group(function () {
            Route::get('/student-attendance', 'index')->name('student-attendance.index');
            Route::get('/student-attendance/{session_type}/create', 'create')->name('student-attendance.create');
            Route::post('/student-attendance/{session_type}/create', 'store')->name('student-attendance.store');
            Route::post('/student-attendance/{summary}/{session_type}/update', 'update')->name('student-attendance.update');
        });

        Route::controller(TeacherAttendanceController::class)->group(function () {
            Route::get('/teacher-attendance', 'index')->name('teacher-attendance.index');
            Route::get('/teacher-attendance/{schedule}/create', 'create')->name('teacher-attendance.create');
            Route::post('/teacher-attendance/{schedule}/create', 'store')->name('teacher-attendance.store');
            Route::post('/teacher-attendance/{summary}/update', 'update')->name('teacher-attendance.update');
        });
    });

    Route::middleware('can:isGuru')->group(function () {
        Route::controller(SubjectAttendanceController::class)->group(function () {
            Route::get('/subject-attendance', 'index')->name('subject-attendance.index');
            Route::get('/subject-attendance/{schedule}/create', 'create')->name('subject-attendance.create');
            Route::post('/subject-attendance/{schedule}/create', 'store')->name('subject-attendance.store');
            Route::post('/subject-attendance/{summary}/update', 'update')->name('subject-attendance.update');
        });
    });
});