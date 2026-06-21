<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('staff')->check()) {
            return redirect()->route('staff.dashboard');
        }
        return view('staff.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'mobile'   => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'mobile'   => $request->mobile,
            'password' => $request->password,
        ];

        if (Auth::guard('staff')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('staff')->user();

            // Ensure the user is actually a staff member
            if ($user->role !== 'staff' || $user->status !== 'active') {
                Auth::guard('staff')->logout();
                return back()->withErrors(['mobile' => 'Your account is not active or not authorized.']);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('staff.dashboard'));
        }

        return back()->withErrors(['mobile' => 'Invalid mobile number or password.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('staff.login');
    }
}
