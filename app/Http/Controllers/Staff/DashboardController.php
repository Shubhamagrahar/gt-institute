<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $staff   = Auth::guard('staff')->user()->load('staffProfile.staffRole');
        $profile = $staff->staffProfile;
        $perms   = $profile?->resolvedPermissions() ?? [];

        return view('staff.dashboard', compact('staff', 'profile', 'perms'));
    }
}
