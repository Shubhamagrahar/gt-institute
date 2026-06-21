<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        return view('student.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'Please enter your mobile number or email.',
            'password.required' => 'Please enter your password.',
        ]);

        $login    = $request->input('login');
        $password = $request->input('password');

        // Try mobile first, then email
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $credentials = [
            $field     => $login,
            'password' => $password,
        ];

        if (Auth::guard('student')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('student')->user();

            if ($user->role !== 'student' || $user->status !== 'active') {
                Auth::guard('student')->logout();
                return back()->withErrors(['login' => 'Your account is not active.'])->withInput();
            }

            $request->session()->regenerate();
            return redirect()->intended(route('student.dashboard'));
        }

        return back()->withErrors(['login' => 'Invalid credentials. Please check your mobile/email and password.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('student.login');
    }
}
