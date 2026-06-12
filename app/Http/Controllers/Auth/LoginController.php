<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginOtpMail;
use App\Models\SuperAdmin;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    private const OTP_EXPIRY_MINUTES  = 10;
    private const OTP_RESEND_COOLDOWN = 60;

    public function showLogin()
    {
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

        // ── Super Admin ───────────────────────────────────────────────────────
        $admin = SuperAdmin::where('email', $login)
            ->orWhere('mobile', $login)
            ->orWhere('admin_id', $login)
            ->first();

        if ($admin && Hash::check($password, $admin->password)) {
            if ($admin->status === 'inactive') {
                return back()->withErrors(['login' => 'Your admin account is deactivated.'])->withInput();
            }
            // Super admin — direct login, no OTP
            Auth::guard('web')->login($admin, $remember);
            $request->session()->regenerate();
            return redirect()->route('owner.dashboard');
        }

        // ── Institute User ────────────────────────────────────────────────────
        $user = User::where('email', $login)
            ->orWhere('mobile', $login)
            ->orWhere('user_id', $login)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            if ($user->status === 'inactive') {
                return back()->withErrors(['login' => 'Your account has been deactivated. Contact your institute.'])->withInput();
            }
            return $this->initiateOtpFlow($request, $user, $remember);
        }

        return back()
            ->withErrors(['login' => 'Invalid credentials. Please check your ID / Email / Mobile and password.'])
            ->withInput();
    }

    public function showOtpVerify(Request $request)
    {
        if (!$request->session()->has('otp_pending')) {
            return redirect()->route('login');
        }
        return view('auth.otp-verify', [
            'maskedEmail' => $this->maskEmail($request->session()->get('otp_pending.email')),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $pending = $request->session()->get('otp_pending');

        if (!$pending) {
            return redirect()->route('login')
                ->withErrors(['login' => 'Session expired. Please sign in again.']);
        }

        $record = DB::table('login_otps')->where('email', $pending['email'])->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'OTP not found. Please request a new one.']);
        }

        if (Carbon::parse($record->expires_at)->isPast()) {
            DB::table('login_otps')->where('email', $pending['email'])->delete();
            $request->session()->forget('otp_pending');
            return redirect()->route('login')
                ->withErrors(['login' => 'OTP has expired. Please sign in again.']);
        }

        if ($request->input('otp') !== $record->otp) {
            return back()->withErrors(['otp' => 'Incorrect OTP. Please try again.']);
        }

        // OTP valid — clean up and complete login
        DB::table('login_otps')->where('email', $pending['email'])->delete();
        $request->session()->forget('otp_pending');

        $user = User::find($pending['account_id']);

        if (!$user || $user->status === 'inactive') {
            return redirect()->route('login')
                ->withErrors(['login' => 'Account not found or deactivated.']);
        }

        Auth::guard('institute')->login($user, $pending['remember'] ?? false);
        $request->session()->regenerate();

        return $this->redirectAfterLogin($user);
    }

    public function resendOtp(Request $request)
    {
        $pending = $request->session()->get('otp_pending');

        if (!$pending) {
            return redirect()->route('login');
        }

        $existing = DB::table('login_otps')->where('email', $pending['email'])->first();
        if ($existing) {
            $elapsed = (int) Carbon::parse($existing->created_at)->diffInSeconds(now());
            if ($elapsed < self::OTP_RESEND_COOLDOWN) {
                $wait = self::OTP_RESEND_COOLDOWN - $elapsed;
                return back()->withErrors(['otp' => "Please wait {$wait} seconds before requesting a new OTP."]);
            }
        }

        $otp     = $this->generateAndStoreOtp($pending['email']);
        $account = User::with('profile')->find($pending['account_id']);
        $name    = (string) ($account?->profile?->name ?? $account?->user_id ?? $pending['email']);

        Mail::to($pending['email'])->send(new LoginOtpMail($name, $otp));

        return back()->with('success', 'A new OTP has been sent to your email address.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('institute')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function initiateOtpFlow(Request $request, User $user, bool $remember)
    {
        $email = $user->email ?? null;

        // OTP disabled from Owner panel or no email — direct login
        if (!SystemSetting::isOtpEnabled() || !$email) {
            Auth::guard('institute')->login($user, $remember);
            $request->session()->regenerate();
            return $this->redirectAfterLogin($user);
        }

        $otp = $this->generateAndStoreOtp($email);

        $user->loadMissing('profile');
        $name = (string) ($user->profile?->name ?? $user->user_id);

        Mail::to($email)->send(new LoginOtpMail($name, $otp));

        $request->session()->put('otp_pending', [
            'account_id' => $user->getKey(),
            'email'      => $email,
            'remember'   => $remember,
        ]);

        return redirect()->route('login.otp.show');
    }

    private function redirectAfterLogin(User $user)
    {
        return match ($user->role) {
            'institute_head', 'staff'           => redirect()->route('institute.dashboard'),
            'franchise_head', 'franchise_staff' => redirect()->route('franchise.dashboard'),
            'student'                           => redirect()->route('institute.dashboard'),
            'franchise_student'                 => redirect()->route('franchise.dashboard'),
            default                             => redirect('/'),
        };
    }

    private function generateAndStoreOtp(string $email): string
    {
        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('login_otps')->where('email', $email)->delete();
        DB::table('login_otps')->insert([
            'email'      => $email,
            'guard'      => 'institute',
            'otp'        => $otp,           // plain — 10 min expiry, single-use
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $otp;
    }

    private function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) {
            return $email;
        }

        [$name, $domain] = explode('@', $email, 2);

        $visible      = substr($name, 0, min(2, strlen($name)));
        $maskedName   = $visible . str_repeat('*', max(strlen($name) - strlen($visible), 2));
        $domainParts  = explode('.', $domain);
        $domainName   = $domainParts[0] ?? '';
        $domainExt    = implode('.', array_slice($domainParts, 1));
        $maskedDomain = substr($domainName, 0, 1) . str_repeat('*', max(strlen($domainName) - 1, 2));

        return $maskedName . '@' . $maskedDomain . ($domainExt ? '.' . $domainExt : '');
    }
}
