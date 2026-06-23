<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OwnerLoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('owner.dashboard');
        }
        return view('auth.owner-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $login    = trim($request->input('login'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        $admin = SuperAdmin::where('email', $login)
            ->orWhere('mobile', $login)
            ->orWhere('admin_id', $login)
            ->first();

        if ($admin && Hash::check($password, $admin->password)) {
            if ($admin->status === 'inactive') {
                return back()->withErrors(['login' => 'Your admin account is deactivated.'])->withInput();
            }
            Auth::guard('web')->login($admin, $remember);
            $request->session()->regenerate();
            return redirect()->route('owner.dashboard');
        }

        return back()
            ->withErrors(['login' => 'Invalid credentials.'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('owner.login');
    }
}
