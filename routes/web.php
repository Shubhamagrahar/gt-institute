<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboard;
use App\Http\Controllers\Owner\FeatureController;
use App\Http\Controllers\Owner\PlanController;
use App\Http\Controllers\Owner\InstituteController;
use App\Http\Controllers\Institute\DashboardController as InstituteDashboard;
use App\Http\Controllers\Institute\StudentController;
use App\Http\Controllers\Institute\StaffController;
use App\Http\Controllers\Institute\CourseController;
use App\Http\Controllers\Institute\CourseTypeController;
use App\Http\Controllers\Institute\FeeController;
use App\Http\Controllers\Institute\AttendanceController;

// Root redirect
Route::get('/', fn() => redirect()->route('login'));

// Auth routes
Route::middleware('guest:web,institute')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Owner Panel — guard: web
Route::prefix('owner')
    ->name('owner.')
    ->middleware(['auth:web', 'role:owner'])
    ->group(function () {
        Route::get('/dashboard', [OwnerDashboard::class, 'index'])->name('dashboard');
        Route::resource('features', FeatureController::class);
        Route::patch('features/{feature}/toggle', [FeatureController::class, 'toggle'])->name('features.toggle');
        Route::resource('plans', PlanController::class);
        Route::patch('plans/{plan}/toggle', [PlanController::class, 'toggle'])->name('plans.toggle');
        Route::resource('institutes', InstituteController::class);
        Route::get('institutes/{institute}/transactions', [InstituteController::class, 'transactions'])->name('institutes.transactions');
        Route::post('institutes/{institute}/payment', [InstituteController::class, 'recordPayment'])->name('institutes.payment');
        Route::patch('institutes/{institute}/toggle', [InstituteController::class, 'toggle'])->name('institutes.toggle');
        Route::post('institutes/{institute}/resend-credentials', [InstituteController::class, 'resendCredentials'])->name('institutes.resend-credentials');
    });

// Institute Panel — guard: institute
Route::prefix('dashboard')
    ->name('institute.')
    ->middleware(['auth:institute', 'role:institute_head,staff'])
    ->group(function () {
        Route::get('/', [InstituteDashboard::class, 'index'])->name('dashboard');
        Route::resource('students', StudentController::class);
        Route::get('students/{student}/ledger', [StudentController::class, 'ledger'])->name('students.ledger');
        Route::resource('staff', StaffController::class);
        Route::resource('courses', CourseController::class);
        Route::patch('courses/{course}/toggle', [CourseController::class, 'toggle'])->name('courses.toggle');
        Route::resource('course-types', CourseTypeController::class)
            ->parameters(['course-types' => 'courseType'])
            ->except(['show', 'create']);
        Route::get('course-enrollment', [CourseController::class, 'enrollmentList'])->name('courses.enrollments');
        Route::post('course-enrollment/{student}', [CourseController::class, 'enroll'])->name('courses.enroll');
        Route::get('fee', [FeeController::class, 'index'])->name('fee.index');
        Route::post('fee/collect', [FeeController::class, 'collect'])->name('fee.collect');
        Route::get('fee/{student}/history', [FeeController::class, 'history'])->name('fee.history');
        Route::get('attendance/student', [AttendanceController::class, 'studentIndex'])->name('attendance.student');
        Route::post('attendance/student/mark', [AttendanceController::class, 'markStudent'])->name('attendance.student.mark');
        Route::get('attendance/staff', [AttendanceController::class, 'staffIndex'])->name('attendance.staff');
        Route::post('attendance/staff/mark', [AttendanceController::class, 'markStaff'])->name('attendance.staff.mark');
    });
