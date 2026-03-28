<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($request->login);

        // Detect field: email → email, numeric → mobile, else → user_id
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } elseif (is_numeric($login)) {
            $field = 'mobile';
        } else {
            $field = 'user_id';
        }

        // For email and mobile, use Auth::attempt directly
        if ($field !== 'user_id') {
            $credentials = [$field => $login, 'password' => $request->password];
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                return $this->authenticated($request);
            }
        }

        // For user_id (or as fallback), manually find user and verify password
        // Auth::attempt doesn't work with custom primary login fields by default
        $user = \App\Models\User::where('user_id', $login)
            ->orWhere('email', $login)
            ->orWhere('mobile', $login)
            ->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return $this->authenticated($request);
        }

        return back()->withErrors(['login' => 'Invalid credentials. Please check your User ID / Email / Mobile and password.'])->withInput();
    }

    private function authenticated(Request $request)
    {
        $user = Auth::user();

        if ($user->status === 'inactive') {
            Auth::logout();
            return back()->withErrors(['login' => 'Your account has been deactivated. Contact administrator.']);
        }

        return match ($user->role) {
            'owner'          => redirect()->route('owner.dashboard'),
            'institute_head' => redirect()->route('institute.dashboard'),
            'staff'          => redirect()->route('institute.dashboard'),
            default          => redirect('/'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
