<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function profile()
    {
        $user      = Auth::guard('institute')->user();
        $institute = $user->institute;
        $states    = State::orderBy('name')->pluck('name');

        $districtsByState = \App\Models\District::query()
            ->join('states', 'states.id', '=', 'districts.state_id')
            ->select('districts.name as district_name', 'states.name as state_name')
            ->orderBy('districts.name')
            ->get()
            ->groupBy('state_name')
            ->map(fn($rows) => $rows->pluck('district_name')->values());

        return view('institute.accounts.profile', compact('institute', 'states', 'districtsByState'));
    }

    public function updateProfile(Request $request)
    {
        $user      = Auth::guard('institute')->user();
        $institute = $user->institute;

        $validated = $request->validate([
            'short_name'   => 'nullable|string|max:50',
            'mobile'       => 'required|string|max:15',
            'owner_name'   => 'required|string|max:100',
            'owner_mobile' => 'required|string|max:15',
            'address'      => 'nullable|string|max:500',
            'state'        => 'nullable|string|max:60',
            'district'     => 'nullable|string|max:60',
            'pin_code'     => 'nullable|string|max:10',
            'website'      => 'nullable|url|max:150',
            'seat_booking_validity_days' => 'nullable|integer|min:1|max:365',
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'signature'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
            'use_signature' => 'nullable|boolean',
            'stamp'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
            'use_stamp'     => 'nullable|boolean',
        ]);

        $validated['use_signature'] = $request->boolean('use_signature');
        $validated['use_stamp']     = $request->boolean('use_stamp');

        foreach (['logo', 'signature', 'stamp'] as $field) {
            if ($request->hasFile($field)) {
                $file     = $request->file($field);
                $filename = 'inst_' . $institute->id . '_' . $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/institute-logos'), $filename);
                $validated[$field] = 'images/institute-logos/' . $filename;
            } else {
                unset($validated[$field]);
            }
        }

        $institute->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function billing()
    {
        $user = Auth::guard('institute')->user();
        $institute = $user->institute()
            ->with([
                'subscription.plan',
                'wallet',
                'payCollects' => fn ($query) => $query->where('status', 'received')->latest('date'),
            ])
            ->firstOrFail();

        $subscription = $institute->subscription;
        $today = Carbon::today();

        $daysRemaining = null;
        $subscriptionStatus = 'No active plan';

        if ($subscription) {
            $endDate = Carbon::parse($subscription->end_date);
            $daysRemaining = max($today->diffInDays($endDate, false), 0);

            if ($endDate->isPast()) {
                $subscriptionStatus = 'Expired';
            } elseif ($daysRemaining <= 7) {
                $subscriptionStatus = 'Expiring soon';
            } else {
                $subscriptionStatus = 'Active';
            }
        }

        $totalPaid = (float) $institute->payCollects->sum('amt');
        $totalDebits = (float) $institute->transactions()->sum('debit');
        $totalCredits = (float) $institute->transactions()->sum('credit');
        $totalDue = max($totalDebits - $totalPaid, 0);
        $walletBalance = (float) ($institute->wallet?->main_b ?? ($totalPaid - $totalDebits));

        return view('institute.accounts.billing', [
            'institute' => $institute,
            'subscription' => $subscription,
            'daysRemaining' => $daysRemaining,
            'subscriptionStatus' => $subscriptionStatus,
            'totalPaid' => $totalPaid,
            'totalDebits' => $totalDebits,
            'totalCredits' => $totalCredits,
            'totalDue' => $totalDue,
            'walletBalance' => $walletBalance,
            'recentPayments' => $institute->payCollects->take(10),
        ]);
    }

    public function security()
    {
        $user = Auth::guard('institute')->user();
        return view('institute.accounts.security', compact('user'));
    }

    public function generateBackupOtp(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::guard('institute')->user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password. Please try again.']);
        }

        // Plain 6-digit numeric OTP — stored as-is so it can be shown in panel
        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'backup_otp'        => $otp,
            'backup_otp_set_at' => now(),
        ]);

        return back()->with('success', 'Emergency OTP generated successfully.');
    }

    public function showEmergencyCode(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $user = Auth::guard('institute')->user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $institute = $user->institute;

        if (!$institute || !$institute->emergency_otp_secret) {
            return back()->withErrors(['password' => 'Emergency code not configured for your institute. Contact support.']);
        }

        return back()->with('emergency_code', $institute->todayEmergencyCode());
    }

    public function editPassword()
    {
        return view('institute.accounts.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => ['required', 'string'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'max:64',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
                'different:old_password',
                'same:confirm_password',
            ],
            'confirm_password' => ['required', 'string'],
        ], [
            'new_password.regex' => 'New password must include uppercase, lowercase, number, and special character.',
            'new_password.same' => 'New password and confirm password must match.',
            'new_password.different' => 'New password must be different from old password.',
        ]);

        $user = Auth::guard('institute')->user();

        if (! Hash::check($request->old_password, $user->password)) {
            return back()
                ->withErrors(['old_password' => 'Old password is incorrect.'])
                ->withInput($request->except(['old_password', 'new_password', 'confirm_password']));
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()
            ->route('institute.accounts.password.edit')
            ->with('success', 'Password changed successfully.');
    }
}
