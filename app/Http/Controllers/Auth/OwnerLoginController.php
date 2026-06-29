<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginOtpMail;
use App\Models\SuperAdmin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OwnerLoginController extends Controller
{
    private const OTP_EXPIRY_MINUTES  = 10;
    private const OTP_RESEND_COOLDOWN = 60;

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

            // If admin has an email, send OTP
            if ($admin->email) {
                return $this->initiateOtpFlow($request, $admin, $remember);
            }

            // No email — direct login
            Auth::guard('web')->login($admin, $remember);
            $request->session()->regenerate();
            return redirect()->route('owner.dashboard');
        }

        return back()
            ->withErrors(['login' => 'Invalid credentials.'])
            ->withInput();
    }

    public function showOtpVerify(Request $request)
    {
        if (!$request->session()->has('owner_otp_pending')) {
            return redirect()->route('owner.login');
        }
        $pending = $request->session()->get('owner_otp_pending');
        return view('auth.owner-otp-verify', [
            'maskedEmail' => $this->maskEmail($pending['email']),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $pending = $request->session()->get('owner_otp_pending');

        if (!$pending) {
            return redirect()->route('owner.login')
                ->withErrors(['login' => 'Session expired. Please sign in again.']);
        }

        $record = DB::table('login_otps')
            ->where('email', $pending['email'])
            ->where('guard', 'web')
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'OTP not found. Please request a new one.']);
        }

        if (Carbon::parse($record->expires_at)->isPast()) {
            DB::table('login_otps')->where('email', $pending['email'])->where('guard', 'web')->delete();
            $request->session()->forget('owner_otp_pending');
            return redirect()->route('owner.login')
                ->withErrors(['login' => 'OTP has expired. Please sign in again.']);
        }

        // Block after 5 wrong attempts
        if (($record->attempts ?? 0) >= 5) {
            DB::table('login_otps')->where('email', $pending['email'])->where('guard', 'web')->delete();
            $request->session()->forget('owner_otp_pending');
            return redirect()->route('owner.login')
                ->withErrors(['login' => 'Too many incorrect OTP attempts. Please sign in again.']);
        }

        if ($request->input('otp') !== $record->otp) {
            DB::table('login_otps')->where('email', $pending['email'])->where('guard', 'web')
                ->increment('attempts');
            $remaining = 4 - ($record->attempts ?? 0);
            return back()->withErrors(['otp' => "Incorrect OTP. {$remaining} attempt(s) remaining."]);
        }

        // OTP valid — clean up and complete login
        DB::table('login_otps')->where('email', $pending['email'])->where('guard', 'web')->delete();
        $request->session()->forget('owner_otp_pending');

        $admin = SuperAdmin::find($pending['account_id']);

        if (!$admin || $admin->status === 'inactive') {
            return redirect()->route('owner.login')
                ->withErrors(['login' => 'Account not found or deactivated.']);
        }

        Auth::guard('web')->login($admin, $pending['remember'] ?? false);
        $request->session()->regenerate();

        return redirect()->route('owner.dashboard');
    }

    public function resendOtp(Request $request)
    {
        $pending = $request->session()->get('owner_otp_pending');

        if (!$pending) {
            return redirect()->route('owner.login');
        }

        $existing = DB::table('login_otps')
            ->where('email', $pending['email'])
            ->where('guard', 'web')
            ->first();

        if ($existing) {
            $elapsed = (int) Carbon::parse($existing->created_at)->diffInSeconds(now());
            if ($elapsed < self::OTP_RESEND_COOLDOWN) {
                $wait = self::OTP_RESEND_COOLDOWN - $elapsed;
                return back()->withErrors(['otp' => "Please wait {$wait} seconds before requesting a new OTP."]);
            }
        }

        $otp = $this->generateAndStoreOtp($pending['email']);
        Mail::to($pending['email'])->send(new LoginOtpMail($pending['name'], $otp));

        return back()->with('success', 'A new OTP has been sent to your email address.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('owner.login');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function initiateOtpFlow(Request $request, SuperAdmin $admin, bool $remember)
    {
        $otp = $this->generateAndStoreOtp($admin->email);
        $name = $admin->name ?? $admin->admin_id ?? $admin->email;

        Mail::to($admin->email)->send(new LoginOtpMail($name, $otp));

        $request->session()->put('owner_otp_pending', [
            'account_id' => $admin->getKey(),
            'email'      => $admin->email,
            'name'       => $name,
            'remember'   => $remember,
        ]);

        return redirect()->route('owner.login.otp.show');
    }

    private function generateAndStoreOtp(string $email): string
    {
        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('login_otps')->where('email', $email)->where('guard', 'web')->delete();
        DB::table('login_otps')->insert([
            'email'      => $email,
            'guard'      => 'web',
            'otp'        => $otp,
            'attempts'   => 0,
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
