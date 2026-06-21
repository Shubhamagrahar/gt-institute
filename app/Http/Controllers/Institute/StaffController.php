<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Mail};
use App\Models\{User, StaffProfile, StaffRole, SalaryRecord, SalaryTransaction, State, District};
use Illuminate\Support\Facades\Schema;
use App\Mail\StaffWelcomeMail;
use Carbon\Carbon;

class StaffController extends Controller
{
    private function institute() { return Auth::guard('institute')->user()->institute; }

    // ── INDEX ─────────────────────────────────────────────────────────────────
    public function index()
    {
        $institute = $this->institute();
        $staff = User::with(['staffProfile.staffRole'])
            ->where('institute_id', $institute->id)
            ->where('role', 'staff')
            ->latest()
            ->paginate(20);

        $roles = StaffRole::where('institute_id', $institute->id)
            ->where('status', 'active')
            ->get();

        return view('institute.staff.index', compact('staff', 'roles'));
    }

    // ── CREATE ────────────────────────────────────────────────────────────────
    public function create()
    {
        $roles = StaffRole::where('institute_id', $this->institute()->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        if ($roles->isEmpty()) {
            return redirect()->route('institute.staff-roles.create')
                ->with('error', 'Please create at least one staff role before adding staff.');
        }

        $states = State::orderBy('name')->pluck('name');
        $districtsByState = Schema::hasTable('districts')
            ? District::query()
                ->select('districts.name as district_name', 'states.name as state_name')
                ->join('states', 'states.id', '=', 'districts.state_id')
                ->orderBy('states.name')
                ->orderBy('districts.name')
                ->get()
                ->groupBy('state_name')
                ->map(fn ($rows) => $rows->pluck('district_name')->values())
                ->toArray()
            : [];

        return view('institute.staff.create', compact('roles', 'states', 'districtsByState'));
    }

    // ── STORE ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $institute = $this->institute();

        $data = $request->validate([
            // Required
            'name'          => 'required|string|max:100',
            'mobile'        => ['required', 'string', 'digits:10', 'unique:users,mobile'],
            'staff_role_id' => 'required|exists:staff_roles,id',
            'joining_date'  => 'required|date',
            'gender'        => 'required|in:male,female,other',
            'salary'        => 'required|numeric|min:0',

            // Contact
            'email'         => 'required|email|max:100|unique:users,email',
            'whatsapp'      => 'nullable|digits:10',

            // Optional personal
            'dob'           => 'nullable|date|before:today',
            'father_name'   => 'nullable|string|max:100',
            'blood_group'   => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'qualification' => 'nullable|string|max:120',
            'experience_years' => 'nullable|integer|min:0|max:60',

            // Address
            'address'       => 'nullable|string|max:300',
            'city'          => 'nullable|string|max:80',
            'state'         => 'nullable|string|max:80',
            'pin'           => 'nullable|digits:6',

            // Documents
            'aadhar_no'     => 'nullable|digits:12',
            'pan_no'        => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],

            // Bank
            'bank_name'     => 'nullable|string|max:100',
            'account_no'    => 'nullable|string|max:30',
            'ifsc'          => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'branch_name'   => 'nullable|string|max:100',

            // Emergency
            'emergency_name'     => 'nullable|string|max:100',
            'emergency_phone'    => 'nullable|digits:10',
            'emergency_relation' => 'nullable|string|max:50',

            'notes'         => 'nullable|string|max:500',
        ], [
            'mobile.digits'     => 'Mobile must be exactly 10 digits.',
            'mobile.unique'     => 'This mobile number is already registered.',
            'email.required'    => 'Email is required to send login credentials.',
            'email.unique'      => 'This email is already registered.',
            'whatsapp.digits'   => 'WhatsApp must be exactly 10 digits.',
            'pin.digits'        => 'PIN code must be 6 digits.',
            'aadhar_no.digits'  => 'Aadhar must be exactly 12 digits.',
            'pan_no.regex'      => 'PAN format invalid. Example: ABCDE1234F',
            'ifsc.regex'        => 'IFSC format invalid. Example: SBIN0001234',
            'emergency_phone.digits' => 'Emergency contact phone must be 10 digits.',
        ]);

        $role = StaffRole::findOrFail($data['staff_role_id']);
        abort_unless($role->institute_id === $institute->id, 403);

        // Generate staff ID: {inst_code}/{role_code}/{year}/{seq}
        $instCode = strtoupper($institute->short_name ?? substr($institute->name, 0, 2));
        $roleCode = strtoupper($role->short_code);
        $year     = Carbon::parse($data['joining_date'])->year;
        $seq      = User::where('institute_id', $institute->id)
                        ->where('role', 'staff')
                        ->whereYear('created_at', $year)
                        ->count() + 1;
        $staffId  = "{$instCode}/{$roleCode}/{$year}/" . str_pad($seq, 3, '0', STR_PAD_LEFT);

        // Auto-generate password
        $plain = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ'), 0, 4))
               . rand(100, 999);

        DB::transaction(function () use ($data, $institute, $staffId, $plain) {
            $user = User::create([
                'user_id'      => $staffId,
                'mobile'       => $data['mobile'],
                'email'        => $data['email'] ?? null,
                'password'     => Hash::make($plain),
                'role'         => 'staff',
                'institute_id' => $institute->id,
                'status'       => 'active',
            ]);

            StaffProfile::create([
                'user_id'            => $user->id,
                'institute_id'       => $institute->id,
                'staff_role_id'      => $data['staff_role_id'],
                'name'               => $data['name'],
                'designation'        => $role->name,
                'joining_date'       => $data['joining_date'],
                'gender'             => $data['gender'],
                'salary'             => $data['salary'],
                'salary_type'        => 'monthly',
                'whatsapp'           => $data['whatsapp'] ?? null,
                'dob'                => $data['dob'] ?? null,
                'father_name'        => $data['father_name'] ?? null,
                'blood_group'        => $data['blood_group'] ?? null,
                'qualification'      => $data['qualification'] ?? null,
                'experience_years'   => $data['experience_years'] ?? 0,
                'address'            => $data['address'] ?? null,
                'city'               => $data['city'] ?? null,
                'state'              => $data['state'] ?? null,
                'pin'                => $data['pin'] ?? null,
                'aadhar_no'          => $data['aadhar_no'] ?? null,
                'pan_no'             => $data['pan_no'] ?? null,
                'bank_name'          => $data['bank_name'] ?? null,
                'account_no'         => $data['account_no'] ?? null,
                'ifsc'               => $data['ifsc'] ?? null,
                'branch_name'        => $data['branch_name'] ?? null,
                'emergency_name'     => $data['emergency_name'] ?? null,
                'emergency_phone'    => $data['emergency_phone'] ?? null,
                'emergency_relation' => $data['emergency_relation'] ?? null,
                'notes'              => $data['notes'] ?? null,
            ]);
        });

        // Send welcome email with credentials
        $emailSent = false;
        try {
            Mail::to($data['email'])->send(new StaffWelcomeMail(
                name:      $data['name'],
                staffId:   $staffId,
                mobile:    $data['mobile'],
                email:     $data['email'],
                password:  $plain,
                institute: $institute,
                loginUrl:  url('/staff/login'),
            ));
            $emailSent = true;
        } catch (\Exception $e) {
            // Email failed — still show credentials on screen
        }

        session()->flash('staff_created', [
            'name'       => $data['name'],
            'staff_id'   => $staffId,
            'mobile'     => $data['mobile'],
            'password'   => $plain,
            'email'      => $data['email'],
            'email_sent' => $emailSent,
        ]);

        return redirect()->route('institute.staff.index')
            ->with('success', 'Staff member added successfully.');
    }

    // ── SHOW ─────────────────────────────────────────────────────────────────
    public function show(User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id && $staff->role === 'staff', 403);
        $staff->load(['staffProfile.staffRole']);

        $recentSalary = SalaryRecord::where('staff_user_id', $staff->id)
            ->with('transactions')
            ->orderByDesc('month')
            ->take(6)
            ->get();

        return view('institute.staff.show', compact('staff', 'recentSalary'));
    }

    // ── EDIT ─────────────────────────────────────────────────────────────────
    public function edit(User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id && $staff->role === 'staff', 403);
        $staff->load('staffProfile.staffRole');

        $roles = StaffRole::where('institute_id', $this->institute()->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('institute.staff.edit', compact('staff', 'roles'));
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────
    public function update(Request $request, User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id && $staff->role === 'staff', 403);

        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'staff_role_id' => 'required|exists:staff_roles,id',
            'joining_date'  => 'required|date',
            'gender'        => 'required|in:male,female,other',
            'salary'        => 'required|numeric|min:0',
            'salary_type'   => 'required|in:monthly,daily,hourly',
            'email'         => 'nullable|email|max:100',
            'whatsapp'      => 'nullable|digits:10',
            'dob'           => 'nullable|date|before:today',
            'father_name'   => 'nullable|string|max:100',
            'blood_group'   => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'qualification' => 'nullable|string|max:120',
            'experience_years' => 'nullable|integer|min:0|max:60',
            'address'       => 'nullable|string|max:300',
            'city'          => 'nullable|string|max:80',
            'state'         => 'nullable|string|max:80',
            'pin'           => 'nullable|digits:6',
            'aadhar_no'     => 'nullable|digits:12',
            'pan_no'        => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
            'bank_name'     => 'nullable|string|max:100',
            'account_no'    => 'nullable|string|max:30',
            'ifsc'          => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'branch_name'   => 'nullable|string|max:100',
            'emergency_name'     => 'nullable|string|max:100',
            'emergency_phone'    => 'nullable|digits:10',
            'emergency_relation' => 'nullable|string|max:50',
            'status'        => 'required|in:active,inactive',
            'notes'         => 'nullable|string|max:500',
        ], [
            'whatsapp.digits'   => 'WhatsApp must be 10 digits.',
            'pin.digits'        => 'PIN must be 6 digits.',
            'aadhar_no.digits'  => 'Aadhar must be 12 digits.',
            'pan_no.regex'      => 'PAN format: ABCDE1234F',
            'ifsc.regex'        => 'IFSC format: SBIN0001234',
        ]);

        $role = StaffRole::findOrFail($data['staff_role_id']);
        abort_unless($role->institute_id === $this->institute()->id, 403);

        DB::transaction(function () use ($staff, $data, $role) {
            $staff->update([
                'email'  => $data['email'] ?? null,
                'status' => $data['status'],
            ]);

            $staff->staffProfile->update([
                'name'               => $data['name'],
                'staff_role_id'      => $data['staff_role_id'],
                'designation'        => $role->name,
                'joining_date'       => $data['joining_date'],
                'gender'             => $data['gender'],
                'salary'             => $data['salary'],
                'salary_type'        => 'monthly',
                'whatsapp'           => $data['whatsapp'] ?? null,
                'dob'                => $data['dob'] ?? null,
                'father_name'        => $data['father_name'] ?? null,
                'blood_group'        => $data['blood_group'] ?? null,
                'qualification'      => $data['qualification'] ?? null,
                'experience_years'   => $data['experience_years'] ?? 0,
                'address'            => $data['address'] ?? null,
                'city'               => $data['city'] ?? null,
                'state'              => $data['state'] ?? null,
                'pin'                => $data['pin'] ?? null,
                'aadhar_no'          => $data['aadhar_no'] ?? null,
                'pan_no'             => $data['pan_no'] ?? null,
                'bank_name'          => $data['bank_name'] ?? null,
                'account_no'         => $data['account_no'] ?? null,
                'ifsc'               => $data['ifsc'] ?? null,
                'branch_name'        => $data['branch_name'] ?? null,
                'emergency_name'     => $data['emergency_name'] ?? null,
                'emergency_phone'    => $data['emergency_phone'] ?? null,
                'emergency_relation' => $data['emergency_relation'] ?? null,
                'notes'              => $data['notes'] ?? null,
            ]);
        });

        return redirect()->route('institute.staff.show', $staff)
            ->with('success', 'Staff profile updated.');
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────
    public function destroy(User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id && $staff->role === 'staff', 403);
        $staff->update(['status' => 'inactive']);
        return redirect()->route('institute.staff.index')
            ->with('success', 'Staff member deactivated.');
    }

    // ── PERMISSIONS ───────────────────────────────────────────────────────────
    public function permissions(User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id && $staff->role === 'staff', 403);
        $staff->load('staffProfile.staffRole');
        $allPermissions = StaffRole::allPermissions();

        $profile = $staff->staffProfile;
        // isCustom = custom_permissions column has data (not null)
        $isCustom    = !is_null($profile->custom_permissions);
        $activePerms = $isCustom
            ? ($profile->custom_permissions ?? [])
            : ($profile->staffRole?->permissions ?? []);

        return view('institute.staff.permissions', compact('staff', 'allPermissions', 'isCustom', 'activePerms'));
    }

    public function savePermissions(Request $request, User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id && $staff->role === 'staff', 403);

        $mode = $request->input('mode'); // 'role' or 'custom'

        if ($mode === 'role') {
            // Sync back to role — clear custom
            $staff->staffProfile->update(['custom_permissions' => null]);
        } else {
            // Save custom permissions
            $perms = $request->input('permissions', []);
            $staff->staffProfile->update(['custom_permissions' => $perms]);
        }

        return redirect()->route('institute.staff.permissions', $staff)
            ->with('success', 'Permissions updated.');
    }

    // ── SALARY ────────────────────────────────────────────────────────────────
    public function salary(User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id && $staff->role === 'staff', 403);
        $staff->load(['staffProfile.staffRole']);

        $institute = $this->institute();
        $profile   = $staff->staffProfile;
        $role      = $profile->staffRole;

        // All salary records for this staff
        $records = SalaryRecord::where('staff_user_id', $staff->id)
            ->with('transactions')
            ->orderByDesc('month')
            ->paginate(12);

        // Current month calculation
        $now          = Carbon::now();
        $monthKey     = $now->format('Y-m-01');
        $currentRecord = SalaryRecord::where('staff_user_id', $staff->id)
            ->where('month', $monthKey)
            ->with('transactions')
            ->first();

        // Attendance data for current month (from attendance_staff table if exists)
        $attendanceData = $this->getMonthAttendance($staff->id, $institute->id, $now);

        // Salary suggestion for current month
        $suggestion = $this->calculateSalary($profile, $role, $attendanceData, $now);

        return view('institute.staff.salary', compact(
            'staff', 'profile', 'role', 'records', 'currentRecord', 'suggestion', 'attendanceData', 'now'
        ));
    }

    public function createSalaryRecord(Request $request, User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id && $staff->role === 'staff', 403);

        $data = $request->validate([
            'month'           => 'required|date_format:Y-m',
            'expected_amount' => 'required|numeric|min:0',
        ]);

        $monthDate = $data['month'] . '-01';

        $record = SalaryRecord::firstOrCreate(
            ['staff_user_id' => $staff->id, 'month' => $monthDate],
            [
                'institute_id'    => $this->institute()->id,
                'expected_amount' => $data['expected_amount'],
                'paid_amount'     => 0,
                'status'          => 'pending',
            ]
        );

        return response()->json(['success' => true, 'record_id' => $record->id]);
    }

    public function recordPayment(Request $request, User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id && $staff->role === 'staff', 403);

        $data = $request->validate([
            'salary_record_id' => 'required|exists:salary_records,id',
            'amount'           => 'required|numeric|min:1',
            'payment_date'     => 'required|date',
            'payment_mode'     => 'required|in:cash,bank,upi,cheque',
            'reference_no'     => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:200',
        ]);

        $record = SalaryRecord::findOrFail($data['salary_record_id']);
        abort_unless($record->staff_user_id === $staff->id, 403);

        SalaryTransaction::create([
            'salary_record_id' => $record->id,
            'institute_id'     => $this->institute()->id,
            'amount'           => $data['amount'],
            'payment_date'     => $data['payment_date'],
            'payment_mode'     => $data['payment_mode'],
            'reference_no'     => $data['reference_no'] ?? null,
            'notes'            => $data['notes'] ?? null,
            'created_by'       => Auth::guard('institute')->id(),
        ]);

        $record->recalculate();

        return redirect()->route('institute.staff.salary', $staff)
            ->with('success', 'Payment recorded.');
    }

    public function deleteTransaction(Request $request, SalaryTransaction $txn)
    {
        $institute = $this->institute();
        abort_unless($txn->institute_id === $institute->id, 403);
        $record = $txn->record;
        $txn->delete();
        $record->recalculate();
        return back()->with('success', 'Transaction removed.');
    }

    // ── SALARY SLIP ───────────────────────────────────────────────────────────
    public function salarySlip(User $staff, SalaryRecord $record)
    {
        $institute = $this->institute();
        abort_unless($staff->institute_id === $institute->id && $staff->role === 'staff', 403);
        abort_unless($record->staff_user_id === $staff->id, 403);

        $staff->load(['staffProfile.staffRole']);
        $record->load('transactions');
        $profile = $staff->staffProfile;
        $role    = $profile?->staffRole;

        $month = Carbon::parse($record->month);
        $attendanceData = $this->getMonthAttendance($staff->id, $institute->id, $month);
        $suggestion     = $this->calculateSalary($profile, $role, $attendanceData, $month);

        return view('institute.staff.salary-slip', compact('staff', 'profile', 'role', 'record', 'institute', 'month', 'attendanceData', 'suggestion'));
    }

    // ── HELPERS ───────────────────────────────────────────────────────────────

    private function getMonthAttendance(int $staffId, int $instituteId, Carbon $month): array
    {
        $start = $month->copy()->startOfMonth()->toDateString();
        $end   = $month->copy()->endOfMonth()->toDateString();

        try {
            $rows = DB::table('attendance_staffs')
                ->where('user_id', $staffId)
                ->where('institute_id', $instituteId)
                ->whereBetween('date', [$start, $end])
                ->get();

            $present = $rows->where('status', 'P')->count();
            $absent  = $rows->where('status', 'A')->count();
            $late    = $rows->where('status', 'L')->count();
        } catch (\Exception $e) {
            $present = $absent = $late = 0;
        }

        // Count Sundays in month
        $sundays = 0;
        $d = $month->copy()->startOfMonth();
        while ($d->lte($month->copy()->endOfMonth())) {
            if ($d->dayOfWeek === Carbon::SUNDAY) $sundays++;
            $d->addDay();
        }

        $totalDays   = $month->daysInMonth;
        $workingDays = $totalDays - $sundays;

        return compact('present', 'absent', 'late', 'workingDays', 'sundays', 'totalDays');
    }

    private function calculateSalary(StaffProfile $profile, ?StaffRole $role, array $att, Carbon $month): array
    {
        $monthlySalary = (float) $profile->salary;
        $graceDays     = $role?->grace_days ?? 2;
        $workingDays   = $att['workingDays'];
        $attended      = $att['present'] + $att['late']; // late counts as attended
        $perDay        = round($monthlySalary / 30, 2);
        $required      = max(0, $workingDays - $graceDays);
        $shortfall     = max(0, $required - $attended);
        $deduction     = round($shortfall * $perDay, 2);
        $suggested     = max(0, $monthlySalary - $deduction);

        return compact('monthlySalary', 'graceDays', 'workingDays', 'attended',
                       'perDay', 'required', 'shortfall', 'deduction', 'suggested');
    }
}
