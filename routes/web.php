<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboard;
use App\Http\Controllers\Owner\FeatureController;
use App\Http\Controllers\Owner\PlanController;
use App\Http\Controllers\Owner\InstituteController;
use App\Http\Controllers\Owner\SettingsController;
use App\Http\Controllers\Institute\DashboardController as InstituteDashboard;
use App\Http\Controllers\Institute\StudentController;
use App\Http\Controllers\Institute\StaffController;
use App\Http\Controllers\Institute\CourseController;
use App\Http\Controllers\Institute\CourseTypeController;
use App\Http\Controllers\Institute\FeeController;
use App\Http\Controllers\Institute\AttendanceController;
use App\Http\Controllers\Institute\SubjectController;
use App\Http\Controllers\Institute\SessionController;
use App\Http\Controllers\Institute\BatchController;
use App\Http\Controllers\Institute\FormBuilderController;
use App\Http\Controllers\Institute\FeeTypeController;
use App\Http\Controllers\Institute\PaymentPlanController;
use App\Http\Controllers\Institute\EnrollmentController;
use App\Http\Controllers\Institute\FeeCollectController;
use App\Http\Controllers\Institute\FranchiseController;
use App\Http\Controllers\Institute\FranchiseFeeController;
use App\Http\Controllers\Institute\FranchiseLevelController;
use App\Http\Controllers\Institute\ChannelPartnerController;
use App\Http\Controllers\Institute\AccountController;
use App\Http\Controllers\Institute\FeesDashboardController;
use App\Http\Controllers\Franchise\DashboardController as FranchiseDashboard;

// Root redirect
Route::get('/', fn() => redirect()->route('login'));

// Auth routes
Route::middleware('guest:web,institute')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/login/otp', [LoginController::class, 'showOtpVerify'])->name('login.otp.show');
    Route::post('/login/otp', [LoginController::class, 'verifyOtp'])->name('login.otp.verify');
    Route::post('/login/otp/resend', [LoginController::class, 'resendOtp'])->name('login.otp.resend');
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/forgot-password/sent', [PasswordResetController::class, 'showSentPage'])->name('password.sent');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
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
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });

// Institute Panel — guard: institute
Route::prefix('dashboard')
    ->name('institute.')
    ->middleware(['auth:institute', 'role:institute_head,staff', 'check.session'])
    ->group(function () {

         Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index')->withoutMiddleware('check.session');
        Route::get('/sessions/create', [SessionController::class, 'create'])->name('sessions.create')->withoutMiddleware('check.session');
        Route::post('/sessions', [SessionController::class, 'store'])->name('sessions.store')->withoutMiddleware('check.session');
        Route::patch('/sessions/{session}/toggle', [SessionController::class, 'toggle'])->name('sessions.toggle');
        Route::delete('/sessions/{session}', [SessionController::class, 'destroy'])->name('sessions.destroy');
        Route::post('/sessions/switch', [SessionController::class, 'switch'])->name('sessions.switch')->withoutMiddleware('check.session');
        Route::get('/accounts/billing-subscription', [AccountController::class, 'billing'])->name('accounts.billing')->withoutMiddleware('check.session');
        Route::get('/accounts/change-password', [AccountController::class, 'editPassword'])->name('accounts.password.edit')->withoutMiddleware('check.session');
        Route::post('/accounts/change-password', [AccountController::class, 'updatePassword'])->name('accounts.password.update')->withoutMiddleware('check.session');
        Route::get('/accounts/security', [AccountController::class, 'security'])->name('accounts.security')->withoutMiddleware('check.session');
        Route::post('/accounts/security/backup-otp', [AccountController::class, 'generateBackupOtp'])->name('accounts.backup-otp.generate')->withoutMiddleware('check.session');
        Route::post('/accounts/emergency-code', [AccountController::class, 'showEmergencyCode'])->name('accounts.emergency-code')->withoutMiddleware('check.session');
        
        Route::get('/', [InstituteDashboard::class, 'index'])->name('dashboard');
        Route::resource('students', StudentController::class);
        Route::get('students/{student}/ledger', [StudentController::class, 'ledger'])->name('students.ledger');
        Route::get('students/{student}/enrollments/{courseBook}/edit', [EnrollmentController::class, 'editBooking'])->name('students.enrollments.edit');
        Route::put('students/{student}/enrollments/{courseBook}', [EnrollmentController::class, 'updateBooking'])->name('students.enrollments.update');
        Route::resource('staff', StaffController::class);
        Route::resource('courses', CourseController::class);
        Route::patch('courses/{course}/toggle', [CourseController::class, 'toggle'])->name('courses.toggle');
        Route::get('course-fee-bindings', [CourseController::class, 'feeBindingsIndex'])->name('courses.fee-bindings');
        Route::get('course-fee-bindings/{course}/edit', [CourseController::class, 'feeBindingsEdit'])->name('courses.fee-bindings.edit');
        Route::put('course-fee-bindings/{course}', [CourseController::class, 'feeBindingsUpdate'])->name('courses.fee-bindings.update');
        Route::resource('course-types', CourseTypeController::class)
            ->parameters(['course-types' => 'courseType'])
            ->except(['show', 'create']);
        Route::resource('subjects', SubjectController::class);
        Route::patch('subjects/{subject}/toggle', [SubjectController::class, 'toggle'])->name('subjects.toggle');
        Route::get('batches', [BatchController::class, 'index'])->name('batches.index');
        Route::post('batches', [BatchController::class, 'store'])->name('batches.store');
        Route::patch('batches/{batch}/toggle', [BatchController::class, 'toggle'])->name('batches.toggle');
        Route::delete('batches/{batch}', [BatchController::class, 'destroy'])->name('batches.destroy');
        Route::get('subjects-bind', [SubjectController::class, 'bindIndex'])->name('subjects.bind');
        Route::post('subjects-bind', [SubjectController::class, 'bindStore'])->name('subjects.bind.store');
        Route::delete('subjects-bind/{binding}', [SubjectController::class, 'bindDestroy'])->name('subjects.bind.destroy');
        Route::get('course-enrollment', [CourseController::class, 'enrollmentList'])->name('courses.enrollments');
        Route::post('course-enrollment/{student}', [CourseController::class, 'enroll'])->name('courses.enroll');
        Route::get('fee', [FeeController::class, 'index'])->name('fee.index');
        Route::post('fee/collect', [FeeController::class, 'collect'])->name('fee.collect');
        Route::get('fee/{student}/history', [FeeController::class, 'history'])->name('fee.history');
        Route::get('attendance/student', [AttendanceController::class, 'studentIndex'])->name('attendance.student');
        Route::post('attendance/student/mark', [AttendanceController::class, 'markStudent'])->name('attendance.student.mark');
        Route::get('attendance/staff', [AttendanceController::class, 'staffIndex'])->name('attendance.staff');
        Route::post('attendance/staff/mark', [AttendanceController::class, 'markStaff'])->name('attendance.staff.mark');
    Route::get('form-builder', [FormBuilderController::class, 'index'])->name('form-builder.index');
    Route::get('form-builder/admission', [FormBuilderController::class, 'admission'])->name('form-builder.admission');
    Route::get('form-builder/admission/print', [FormBuilderController::class, 'printAdmission'])->name('form-builder.admission.print');
    Route::post('form-builder/admission', [FormBuilderController::class, 'saveAdmission'])->name('form-builder.admission.save');
    Route::get('form-builder/quick', [FormBuilderController::class, 'quick'])->name('form-builder.quick');
    Route::post('form-builder/quick', [FormBuilderController::class, 'saveQuick'])->name('form-builder.quick.save');

// Fee Types
Route::resource('fee-types', FeeTypeController::class)->except(['show']);

// Payment Plans
        Route::resource('payment-plans', PaymentPlanController::class)->except(['show']);
        Route::resource('franchise-levels', FranchiseLevelController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        // Multi-step create preview/confirm must come BEFORE the resource to avoid {franchise} binding on "create"
        Route::get('franchises/create/preview', [FranchiseController::class, 'preview'])->name('franchises.preview');
        Route::post('franchises/create/confirm', [FranchiseController::class, 'confirmCreate'])->name('franchises.confirm');
        Route::resource('franchises', FranchiseController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::resource('channel-partners', ChannelPartnerController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::patch('channel-partners/{channelPartner}/toggle', [ChannelPartnerController::class, 'toggle'])->name('channel-partners.toggle');
        Route::get('franchises/{franchise}/transactions', [FranchiseController::class, 'transactions'])->name('franchises.transactions');
        Route::patch('franchises/{franchise}/toggle', [FranchiseController::class, 'toggle'])->name('franchises.toggle');
        Route::post('franchises/{franchise}/recharge', [FranchiseController::class, 'recharge'])->name('franchises.recharge');
        Route::post('franchises/{franchise}/recharge-bonus', [FranchiseController::class, 'rechargeBonus'])->name('franchises.recharge-bonus');
        Route::get('franchises/{franchise}/certificate', [FranchiseController::class, 'certificate'])->name('franchises.certificate');
        Route::get('franchise-wallets', [FranchiseController::class, 'walletIndex'])->name('franchises.wallets');
        // Franchise onboarding fee collection (institute ↔ franchise, completely separate from wallet)
        Route::get('franchises/{franchise}/fee', [FranchiseFeeController::class, 'index'])->name('franchises.fee.index');
        Route::post('franchises/{franchise}/fee/collect', [FranchiseFeeController::class, 'collect'])->name('franchises.fee.collect');
        Route::get('franchises/{franchise}/fee/{collection}/receipt', [FranchiseFeeController::class, 'receipt'])->name('franchises.fee.receipt');
        Route::patch('franchises/{franchise}/fee/{collection}/cancel', [FranchiseFeeController::class, 'cancel'])->name('franchises.fee.cancel');

// Fees Dashboard
Route::get('fees-dashboard', [FeesDashboardController::class, 'index'])->name('fees-dashboard');
Route::get('fees-search', [FeesDashboardController::class, 'search'])->name('fees-search');

// Enrollment
Route::get('enrollment/monthly-fees', [EnrollmentController::class, 'monthlyFees'])->name('enrollment.monthly-fees');
Route::get('enrollment/choose', [EnrollmentController::class, 'choose'])->name('enrollment.choose');
Route::get('enrollment/pending', [EnrollmentController::class, 'pending'])->name('enrollment.pending');
Route::post('enrollment/find-student', [EnrollmentController::class, 'findStudent'])->name('enrollment.find-student');
Route::get('enrollment/validate-field', [EnrollmentController::class, 'validateField'])->name('enrollment.validate-field');
Route::get('enrollment/new', [EnrollmentController::class, 'newStudent'])->name('enrollment.new');
Route::post('enrollment/new', [EnrollmentController::class, 'storeNew'])->name('enrollment.store-new');
Route::get('enrollment/quick', [EnrollmentController::class, 'quickStudent'])->name('enrollment.quick');
Route::post('enrollment/quick', [EnrollmentController::class, 'storeQuick'])->name('enrollment.store-quick');
Route::get('enrollment/{courseBook}/profile', [EnrollmentController::class, 'profileForm'])->name('enrollment.profile');
Route::post('enrollment/{courseBook}/profile', [EnrollmentController::class, 'saveProfile'])->name('enrollment.save-profile');
Route::get('enrollment/{courseBook}/fee', [EnrollmentController::class, 'feeForm'])->name('enrollment.fee');
Route::post('enrollment/{courseBook}/fee', [EnrollmentController::class, 'saveFee'])->name('enrollment.save-fee');
Route::get('enrollment/{courseBook}/preview', [EnrollmentController::class, 'preview'])->name('enrollment.preview');
Route::post('enrollment/{courseBook}/confirm', [EnrollmentController::class, 'confirm'])->name('enrollment.confirm');
Route::get('enrollment/{courseBook}/payment-complete', [EnrollmentController::class, 'paymentComplete'])->name('enrollment.payment-complete');
Route::get('enrollment/{courseBook}/receipt/{fee}/a4', [EnrollmentController::class, 'receiptA4'])->name('enrollment.receipt.a4');
Route::get('enrollment/{courseBook}/receipt/{fee}/thermal', [EnrollmentController::class, 'receiptThermal'])->name('enrollment.receipt.thermal');
Route::post('enrollment/{courseBook}/receipt/{fee}/cancel', [EnrollmentController::class, 'cancelFee'])->name('enrollment.receipt.cancel');
Route::post('enrollment/{courseBook}/payment', [EnrollmentController::class, 'addPayment'])->name('enrollment.add-payment');

// Education (AJAX)
Route::post('enrollment/education/add', [EnrollmentController::class, 'addEducation'])->name('enrollment.education.add');
Route::delete('enrollment/education/{education}', [EnrollmentController::class, 'removeEducation'])->name('enrollment.education.remove');

// Fee Collection
Route::get('fee-collect', [FeeCollectController::class, 'index'])->name('fee-collect.index');
Route::get('fee-collect/{user}', [FeeCollectController::class, 'show'])->name('fee-collect.show');
Route::post('fee-collect/{user}/collect', [FeeCollectController::class, 'collect'])->name('fee-collect.collect');
Route::get('fee-collect/{user}/receipt/{fee}', [FeeCollectController::class, 'receipt'])->name('fee-collect.receipt');
    });

Route::prefix('franchise')
    ->name('franchise.')
    ->middleware(['auth:institute', 'role:franchise_head,franchise_staff,franchise_student'])
    ->group(function () {
        Route::get('/dashboard', [FranchiseDashboard::class, 'index'])->name('dashboard');
    });
