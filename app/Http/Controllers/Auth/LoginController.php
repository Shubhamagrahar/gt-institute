<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin()
    {
        // Already logged in? Redirect to correct panel
        if (Auth::guard('web')->check()) {
            return redirect()->route('owner.dashboard');
        }
        if (Auth::guard('institute')->check()) {
            return redirect()->route('institute.dashboard');
        }

        return view('auth.login');
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

        // ── Step 1: Try Super Admin (owner panel) ────────────────────────────
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

        // ── Step 2: Try Institute User (staff / student) ─────────────────────
        $user = User::where('email', $login)
            ->orWhere('mobile', $login)
            ->orWhere('user_id', $login)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            if ($user->status === 'inactive') {
                return back()->withErrors(['login' => 'Your account has been deactivated. Contact your institute.'])->withInput();
            }
            Auth::guard('institute')->login($user, $remember);
            $request->session()->regenerate();

            return match ($user->role) {
                'institute_head', 'staff' => redirect()->route('institute.dashboard'),
                'franchise_head', 'franchise_staff' => redirect()->route('franchise.dashboard'),
                'student'                 => redirect()->route('institute.dashboard'),
                'franchise_student'       => redirect()->route('franchise.dashboard'),
                default                   => redirect('/'),
            };
        }

        // ── Nothing matched ──────────────────────────────────────────────────
        return back()
            ->withErrors(['login' => 'Invalid credentials. Please check your ID / Email / Mobile and password.'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('institute')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
