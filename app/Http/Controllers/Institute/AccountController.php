<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
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
