<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OwnerLoginController;
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
use App\Http\Controllers\Institute\StaffRoleController;
use App\Http\Controllers\Staff\AuthController as StaffAuthController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboard;
use App\Http\Controllers\Student\AuthController as StudentAuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\PasswordResetController as StudentPasswordReset;
use App\Http\Controllers\Franchise\DashboardController as FranchiseDashboard;
use App\Http\Controllers\Franchise\EnrollmentController as FranchiseEnrollment;
use App\Http\Controllers\Franchise\WalletController as FranchiseWallet;
use App\Http\Controllers\Franchise\StudentController as FranchiseStudent;
use App\Http\Controllers\Franchise\CertificateController as FranchiseCertificate;
use App\Http\Controllers\Franchise\PricingController as FranchisePricing;
use App\Http\Controllers\Franchise\BatchController as FranchiseBatch;

// Home page — portal selection
Route::get('/', fn() => view('home'));

// Session expired page — public, no auth required
Route::get('/session-expired', function (\Illuminate\Http\Request $request) {
    $allowed = ['institute', 'owner', 'student', 'staff', 'franchise'];
    $guard   = in_array($request->query('guard'), $allowed) ? $request->query('guard') : 'institute';

    $loginUrls = [
        'institute' => url('/login'),
        'owner'     => url('/owner/login'),
        'student'   => url('/student/login'),
        'staff'     => url('/staff/login'),
        'franchise' => url('/franchise/login'),
    ];

    $labels = [
        'institute' => 'Institute Portal',
        'owner'     => 'Owner Portal',
        'student'   => 'Student Portal',
        'staff'     => 'Staff Portal',
        'franchise' => 'Franchise Portal',
    ];

    $loginUrl   = $loginUrls[$guard];
    $guardLabel = $labels[$guard];

    return view('auth.session-expired', compact('guard', 'loginUrl', 'guardLabel'));
})->name('session.expired');

// ── Owner (Super Admin) Auth ──────────────────────────────────────────────────
Route::prefix('owner')->name('owner.')->group(function () {
    Route::middleware('guest:web')->group(function () {
        Route::get('/login',             [OwnerLoginController::class, 'showLogin'])->name('login');
        Route::post('/login',            [OwnerLoginController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
        Route::get('/login/otp',         [OwnerLoginController::class, 'showOtpVerify'])->name('login.otp.show');
        Route::post('/login/otp',        [OwnerLoginController::class, 'verifyOtp'])->name('login.otp.verify')->middleware('throttle:5,1');
        Route::post('/login/otp/resend', [OwnerLoginController::class, 'resendOtp'])->name('login.otp.resend')->middleware('throttle:3,1');
    });
    Route::post('/logout', [OwnerLoginController::class, 'logout'])->name('logout');
});

// ── Institute Auth ────────────────────────────────────────────────────────────
Route::middleware('guest:institute')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::get('/franchise/login', fn() => view('auth.franchise-login'))->name('franchise.login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    Route::get('/login/otp', [LoginController::class, 'showOtpVerify'])->name('login.otp.show');
    Route::post('/login/otp', [LoginController::class, 'verifyOtp'])->name('login.otp.verify')->middleware('throttle:5,1');
    Route::post('/login/otp/resend', [LoginController::class, 'resendOtp'])->name('login.otp.resend')->middleware('throttle:3,1');
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/forgot-password/sent', [PasswordResetController::class, 'showSentPage'])->name('password.sent');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,1');
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
        Route::get('/accounts/profile', [AccountController::class, 'profile'])->name('accounts.profile')->withoutMiddleware('check.session');
        Route::put('/accounts/profile', [AccountController::class, 'updateProfile'])->name('accounts.profile.update')->withoutMiddleware('check.session');
        Route::get('/accounts/billing-subscription', [AccountController::class, 'billing'])->name('accounts.billing')->withoutMiddleware('check.session');
        Route::get('/accounts/change-password', [AccountController::class, 'editPassword'])->name('accounts.password.edit')->withoutMiddleware('check.session');
        Route::post('/accounts/change-password', [AccountController::class, 'updatePassword'])->name('accounts.password.update')->withoutMiddleware('check.session');
        Route::get('/accounts/security', [AccountController::class, 'security'])->name('accounts.security')->withoutMiddleware('check.session');
        Route::post('/accounts/security/backup-otp', [AccountController::class, 'generateBackupOtp'])->name('accounts.backup-otp.generate')->withoutMiddleware('check.session');
        Route::post('/accounts/emergency-code', [AccountController::class, 'showEmergencyCode'])->name('accounts.emergency-code')->withoutMiddleware('check.session');
        
        Route::get('/', [InstituteDashboard::class, 'index'])->name('dashboard');
        Route::get('students/expired', [StudentController::class, 'expired'])->name('students.expired');
        Route::get('students/closed', [StudentController::class, 'closed'])->name('students.closed');
        Route::get('students/cancelled', [StudentController::class, 'cancelled'])->name('students.cancelled');
        Route::get('students/academic', [StudentController::class, 'academic'])->name('students.academic');
        Route::get('students/suggest', [StudentController::class, 'suggest'])->name('students.suggest');
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
        // Mark Attendance (daily live)
        Route::get('attendance/students', [AttendanceController::class, 'studentIndex'])->name('attendance.students');
        Route::get('attendance/students/batches', [AttendanceController::class, 'getBatches'])->name('attendance.students.batches');
        Route::post('attendance/students/load', [AttendanceController::class, 'loadAttendance'])->name('attendance.students.load');
        Route::post('attendance/students/set-status', [AttendanceController::class, 'setStatus'])->name('attendance.students.set-status');
        Route::post('attendance/students/mark-all', [AttendanceController::class, 'markAll'])->name('attendance.students.mark-all');
        // Attendance Register
        Route::get('attendance/register', [AttendanceController::class, 'registerIndex'])->name('attendance.register');
        Route::post('attendance/register/load', [AttendanceController::class, 'registerLoad'])->name('attendance.register.load');
        Route::post('attendance/register/cell', [AttendanceController::class, 'registerCellUpdate'])->name('attendance.register.cell');
        Route::get('attendance/register/students', [AttendanceController::class, 'registerStudentList'])->name('attendance.register.students');
        Route::get('attendance/register/student-months', [AttendanceController::class, 'studentMonths'])->name('attendance.register.student-months');
        Route::get('attendance/register/export/all', [AttendanceController::class, 'exportAll'])->name('attendance.register.export.all');
        Route::get('attendance/register/export/student', [AttendanceController::class, 'exportStudent'])->name('attendance.register.export.student');
        Route::get('attendance/register/export/month-student', [AttendanceController::class, 'exportMonthStudent'])->name('attendance.register.export.month-student');
        // Student Attendance Report
        Route::get('attendance/student-report', [AttendanceController::class, 'studentReportIndex'])->name('attendance.student-report');
        Route::get('attendance/student-report/search', [AttendanceController::class, 'studentReportSearch'])->name('attendance.student-report.search');
        Route::get('attendance/student-report/load', [AttendanceController::class, 'studentReportLoad'])->name('attendance.student-report.load');
        // Staff Attendance
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
        Route::get('franchise-levels/{franchiseLevel}/charges', [FranchiseLevelController::class, 'chargesStep'])->name('franchise-levels.charges');
        Route::post('franchise-levels/{franchiseLevel}/charges', [FranchiseLevelController::class, 'storeCharges'])->name('franchise-levels.charges.store');
        Route::get('franchise-levels/{franchiseLevel}/charges/edit', [FranchiseLevelController::class, 'editCharges'])->name('franchise-levels.charges.edit');
        Route::post('franchise-levels/{franchiseLevel}/charges/{charge}/update', [FranchiseLevelController::class, 'updateCharge'])->name('franchise-levels.charges.update');
        // Multi-step create: charges → preview → confirm (all before resource to avoid {franchise} binding)
        Route::get('franchises/create/charges', [FranchiseController::class, 'chargesStep'])->name('franchises.charges');
        Route::post('franchises/create/charges', [FranchiseController::class, 'storeCharges'])->name('franchises.charges.store');
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
        Route::post('franchises/{franchise}/resend-credentials', [FranchiseController::class, 'resendCredentials'])->name('franchises.resend-credentials');
        Route::get('franchises/{franchise}/course-charges', [FranchiseController::class, 'courseCharges'])->name('franchises.course-charges');
        Route::post('franchises/{franchise}/grant-course-type/{courseType}', [FranchiseController::class, 'grantCourseType'])->name('franchises.grant-course-type');
        Route::delete('franchises/{franchise}/revoke-course-type/{courseType}', [FranchiseController::class, 'revokeCourseType'])->name('franchises.revoke-course-type');
        Route::patch('franchises/{franchise}/course-charges/{charge}', [FranchiseController::class, 'updateCourseCharge'])->name('franchises.course-charges.update');
        Route::get('franchise-wallets', [FranchiseController::class, 'walletIndex'])->name('franchises.wallets');
        // Franchise onboarding fee collection (institute ↔ franchise, completely separate from wallet)
        Route::get('franchises/{franchise}/fee', [FranchiseFeeController::class, 'index'])->name('franchises.fee.index');
        Route::post('franchises/{franchise}/fee/collect', [FranchiseFeeController::class, 'collect'])->name('franchises.fee.collect');
        Route::get('franchises/{franchise}/fee/{payment}/receipt', [FranchiseFeeController::class, 'receipt'])->name('franchises.fee.receipt');
        Route::patch('franchises/{franchise}/fee/{payment}/cancel', [FranchiseFeeController::class, 'cancel'])->name('franchises.fee.cancel');

// Certificates & Marksheets
Route::get('certificates',                    [\App\Http\Controllers\Institute\CertificateController::class, 'index'])       ->name('certificates.index');
Route::get('certificates/generate',           [\App\Http\Controllers\Institute\CertificateController::class, 'generate'])    ->name('certificates.generate');
Route::get('certificates/walk-in',            [\App\Http\Controllers\Institute\CertificateController::class, 'walkin'])      ->name('certificates.walkin');
Route::get('certificates/requests',           [\App\Http\Controllers\Institute\CertificateController::class, 'requests'])    ->name('certificates.requests');
Route::get('certificates/history',            [\App\Http\Controllers\Institute\CertificateController::class, 'history'])     ->name('certificates.history');
Route::get('certificates/enrollments/{user}', [\App\Http\Controllers\Institute\CertificateController::class, 'enrollments']) ->name('certificates.enrollments');
Route::post('certificates',                   [\App\Http\Controllers\Institute\CertificateController::class, 'store'])       ->name('certificates.store');
Route::post('certificates/reject',            [\App\Http\Controllers\Institute\CertificateController::class, 'reject'])      ->name('certificates.reject');

// Fees Dashboard
Route::get('fees-dashboard', [FeesDashboardController::class, 'index'])->name('fees-dashboard');
Route::get('fees-search', [FeesDashboardController::class, 'search'])->name('fees-search');
Route::get('fees/collection-report', [FeesDashboardController::class, 'collectionReport'])->name('fees.collection-report');

// Enrollment
Route::get('enrollment/monthly-fees', [EnrollmentController::class, 'monthlyFees'])->name('enrollment.monthly-fees');
Route::get('enrollment/choose', [EnrollmentController::class, 'choose'])->name('enrollment.choose');
Route::get('enrollment/pending', [EnrollmentController::class, 'pending'])->name('enrollment.pending');
Route::match(['GET','POST'], 'enrollment/find-student', [EnrollmentController::class, 'findStudent'])->name('enrollment.find-student');
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
Route::post('enrollment/{courseBook}/renew', [EnrollmentController::class, 'renewBooking'])->name('enrollment.renew');
Route::post('enrollment/{courseBook}/cancel', [EnrollmentController::class, 'cancelBooking'])->name('enrollment.cancel');

// Education (AJAX)
Route::post('enrollment/education/add', [EnrollmentController::class, 'addEducation'])->name('enrollment.education.add');
Route::delete('enrollment/education/{education}', [EnrollmentController::class, 'removeEducation'])->name('enrollment.education.remove');

// Dev email preview route — redirected for security
Route::get('dev/preview-email/seat-booking/{courseBook}', function () {
    return redirect()->route('institute.dashboard');
})->middleware('auth:institute');

// Enquiries
Route::get('enquiries', [\App\Http\Controllers\Institute\EnquiryController::class, 'index'])->name('enquiries.index');
Route::get('enquiries/create', [\App\Http\Controllers\Institute\EnquiryController::class, 'create'])->name('enquiries.create');
Route::post('enquiries', [\App\Http\Controllers\Institute\EnquiryController::class, 'store'])->name('enquiries.store');
Route::get('enquiries/check-duplicate', [\App\Http\Controllers\Institute\EnquiryController::class, 'checkDuplicate'])->name('enquiries.check-duplicate');
Route::get('enquiries/{enquiry}', [\App\Http\Controllers\Institute\EnquiryController::class, 'show'])->name('enquiries.show');
Route::post('enquiries/{enquiry}/followup', [\App\Http\Controllers\Institute\EnquiryController::class, 'storeFollowup'])->name('enquiries.followup');
Route::patch('enquiries/{enquiry}/mark-lost', [\App\Http\Controllers\Institute\EnquiryController::class, 'markLost'])->name('enquiries.mark-lost');
Route::get('enquiries/{enquiry}/convert', [\App\Http\Controllers\Institute\EnquiryController::class, 'convert'])->name('enquiries.convert');

// Quick Pay (kept for now)
Route::get('quick-pay', [FeeCollectController::class, 'quickPay'])->name('quick-pay');
Route::get('quick-pay/search', [FeeCollectController::class, 'quickPaySearch'])->name('quick-pay.search');
// fee-collect pages removed — redirect old URLs to enrollment pending list
Route::get('fee-collect', fn() => redirect()->route('institute.enrollment.pending'))->name('fee-collect.index');
Route::get('fee-collect/{user}', fn() => redirect()->route('institute.enrollment.pending'))->name('fee-collect.show');
Route::get('fee-collect/{user}/receipt/{fee}', fn() => redirect()->route('institute.enrollment.pending'))->name('fee-collect.receipt');

// Wallet Adjustment
Route::get('wallet-adjustment', [\App\Http\Controllers\Institute\WalletAdjustmentController::class, 'index'])->name('wallet-adjustment.index');
Route::get('wallet-adjustment/search', [\App\Http\Controllers\Institute\WalletAdjustmentController::class, 'search'])->name('wallet-adjustment.search');
Route::post('wallet-adjustment/{user}/credit', [\App\Http\Controllers\Institute\WalletAdjustmentController::class, 'credit'])->name('wallet-adjustment.credit');
Route::post('wallet-adjustment/{user}/debit', [\App\Http\Controllers\Institute\WalletAdjustmentController::class, 'debit'])->name('wallet-adjustment.debit');
    });

Route::prefix('franchise')
    ->name('franchise.')
    ->middleware(['auth:institute', 'role:franchise_head,franchise_staff,franchise_student', 'check.session'])
    ->group(function () {

        // ── Dashboard ───────────────────────────────────────────────────────
        Route::get('/dashboard', [FranchiseDashboard::class, 'index'])->name('dashboard');

        // ── Enrollment ──────────────────────────────────────────────────────
        Route::prefix('enrollment')->name('enrollment.')->group(function () {
            Route::get('/pending',           [FranchiseEnrollment::class, 'pending'])->name('pending');
            Route::get('/choose',            [FranchiseEnrollment::class, 'choose'])->name('choose');
            Route::get('/new',               [FranchiseEnrollment::class, 'newStudent'])->name('new');
            Route::post('/new',              [FranchiseEnrollment::class, 'storeNew'])->name('store-new');
            Route::get('/quick',             [FranchiseEnrollment::class, 'quick'])->name('quick');
            Route::post('/quick',            [FranchiseEnrollment::class, 'storeQuick'])->name('store-quick');
            Route::match(['GET','POST'], '/find-student', [FranchiseEnrollment::class, 'findStudent'])->name('find-student');
            Route::post('/existing',         [FranchiseEnrollment::class, 'storeExisting'])->name('store-existing');
            Route::get('/validate-field',    [FranchiseEnrollment::class, 'validateField'])->name('validate-field');

            Route::get('/{courseBook}/profile',  [FranchiseEnrollment::class, 'profileForm'])->name('profile');
            Route::post('/{courseBook}/profile', [FranchiseEnrollment::class, 'saveProfile'])->name('save-profile');

            Route::get('/{courseBook}/fee',  [FranchiseEnrollment::class, 'feeForm'])->name('fee');
            Route::post('/{courseBook}/fee', [FranchiseEnrollment::class, 'saveFee'])->name('save-fee');

            Route::get('/{courseBook}/payment-complete',   [FranchiseEnrollment::class, 'paymentComplete'])->name('payment-complete');
            Route::post('/{courseBook}/confirm',           [FranchiseEnrollment::class, 'confirm'])->name('confirm');
            Route::post('/{courseBook}/add-payment',       [FranchiseEnrollment::class, 'addPayment'])->name('add-payment');

            Route::get('/{courseBook}/receipt/{fee}/a4',      [FranchiseEnrollment::class, 'receiptA4'])->name('receipt.a4');
            Route::get('/{courseBook}/receipt/{fee}/thermal',  [FranchiseEnrollment::class, 'receiptThermal'])->name('receipt.thermal');
            Route::post('/{courseBook}/receipt/{fee}/cancel',  [FranchiseEnrollment::class, 'cancelFee'])->name('receipt.cancel');
        });

        // ── Students ────────────────────────────────────────────────────────
        Route::get('/students',             [FranchiseStudent::class, 'index'])->name('students.index');

        // ── Wallet ──────────────────────────────────────────────────────────
        Route::get('/wallet',               [FranchiseWallet::class, 'index'])->name('wallet');

        // ── Batches ─────────────────────────────────────────────────────────
        Route::get('/batches',                        [FranchiseBatch::class, 'index'])->name('batches.index');
        Route::post('/batches',                       [FranchiseBatch::class, 'store'])->name('batches.store');
        Route::patch('/batches/{batch}/toggle',       [FranchiseBatch::class, 'toggle'])->name('batches.toggle');
        Route::delete('/batches/{batch}',             [FranchiseBatch::class, 'destroy'])->name('batches.destroy');

        // ── Course Pricing ──────────────────────────────────────────────────
        Route::get('/course-pricing',                                        [FranchisePricing::class, 'index'])->name('pricing.index');
        Route::patch('/course-pricing/{charge}',                             [FranchisePricing::class, 'update'])->name('pricing.update');
        Route::post('/course-pricing/{charge}/fee-structures',               [FranchisePricing::class, 'saveFeeStructures'])->name('pricing.fee-structures');
        Route::get('/course-pricing/{charge}/fee-bindings',                  [FranchisePricing::class, 'feeBindingsEdit'])->name('pricing.fee-bindings.edit');
        Route::post('/course-pricing/{charge}/fee-bindings',                 [FranchisePricing::class, 'feeBindingsSave'])->name('pricing.fee-bindings.save');
        Route::delete('/course-pricing/fee-bindings/{binding}',              [FranchisePricing::class, 'feeBindingsRemove'])->name('pricing.fee-bindings.remove');

        // ── Certificate ─────────────────────────────────────────────────────
        Route::get('/certificate',          [FranchiseCertificate::class, 'index'])->name('certificate.index');
        Route::get('/certificate/view',     [FranchiseCertificate::class, 'view'])->name('certificate.view');
    });

// ── STAFF AUTH ────────────────────────────────────────────────────────────────
Route::prefix('staff')->name('staff.')->group(function () {
    Route::middleware('guest:staff')->group(function () {
        Route::get('/login',  [StaffAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [StaffAuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    });
    Route::post('/logout', [StaffAuthController::class, 'logout'])->name('logout');

    // Staff Panel (protected)
    Route::middleware('auth.staff')->group(function () {
        Route::get('/dashboard', [StaffDashboard::class, 'index'])->name('dashboard');
    });
});

// ── INSTITUTE: Staff Role Management (add to institute group above) ───────────
Route::prefix('institute')->name('institute.')->middleware(['auth:institute', 'role:institute_head', 'check.session'])->group(function () {
    Route::resource('staff-roles', StaffRoleController::class)->except(['show']);
    Route::get('staff/{staff}/permissions', [\App\Http\Controllers\Institute\StaffController::class, 'permissions'])->name('staff.permissions');
    Route::post('staff/{staff}/permissions', [\App\Http\Controllers\Institute\StaffController::class, 'savePermissions'])->name('staff.permissions.save');
    Route::get('staff/{staff}/salary', [\App\Http\Controllers\Institute\StaffController::class, 'salary'])->name('staff.salary');
    Route::post('staff/{staff}/salary/record', [\App\Http\Controllers\Institute\StaffController::class, 'createSalaryRecord'])->name('staff.salary.record');
    Route::post('staff/{staff}/salary/pay', [\App\Http\Controllers\Institute\StaffController::class, 'recordPayment'])->name('staff.salary.pay');
    Route::delete('staff/salary/transaction/{txn}', [\App\Http\Controllers\Institute\StaffController::class, 'deleteTransaction'])->name('staff.salary.transaction.delete');
    Route::get('staff/{staff}/salary/{record}/slip', [\App\Http\Controllers\Institute\StaffController::class, 'salarySlip'])->name('staff.salary.slip');
});

// ── STUDENT PORTAL ────────────────────────────────────────────────────────────
Route::prefix('student')->name('student.')->group(function () {
    Route::middleware('guest:student')->group(function () {
        Route::get('/login',  [StudentAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [StudentAuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
        Route::get('/forgot-password',       [StudentPasswordReset::class, 'showForgotForm'])->name('password.request');
        Route::post('/forgot-password',      [StudentPasswordReset::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
        Route::get('/forgot-password/sent',  [StudentPasswordReset::class, 'showSentPage'])->name('password.sent');
        Route::get('/reset-password/{token}',[StudentPasswordReset::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password',       [StudentPasswordReset::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,1');
    });
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:student')->group(function () {
        Route::get('/dashboard',  [StudentDashboard::class, 'dashboard'])->name('dashboard');
        Route::get('/profile',    [StudentDashboard::class, 'profile'])->name('profile');
        Route::get('/fees',       [StudentDashboard::class, 'fees'])->name('fees');
        Route::get('/attendance', [StudentDashboard::class, 'attendance'])->name('attendance');
        Route::get('/courses',    [StudentDashboard::class, 'courses'])->name('courses');
    });
});
