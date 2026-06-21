<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetLinkMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Throwable;

class PasswordResetController extends Controller
{
    private const TOKEN_EXPIRY_MINUTES = 60;

    public function showForgotForm()
    {
        return view('student.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'login' => 'required|string|max:100',
        ]);

        $lookup = trim((string) $request->input('login'));
        $user   = $this->findStudent($lookup);

        if (!$user) {
            return back()
                ->withErrors(['login' => 'No student account was found for that email or mobile number.'])
                ->withInput();
        }

        if ($user->status !== 'active') {
            return back()
                ->withErrors(['login' => 'Your account is inactive. Please contact your institute.'])
                ->withInput();
        }

        if (!$user->email) {
            return back()
                ->withErrors(['login' => 'No email address is linked to this account. Please contact your institute.'])
                ->withInput();
        }

        $plainToken = Str::random(64);

        DB::table('account_password_resets')->updateOrInsert(
            ['account_type' => 'user', 'email' => $user->email],
            ['token' => Hash::make($plainToken), 'created_at' => now()]
        );

        $resetUrl = route('student.password.reset', ['token' => $plainToken])
            . '?' . http_build_query(['email' => $user->email]);

        try {
            Mail::to($user->email)->send(new PasswordResetLinkMail(
                accountName: $user->profile?->name ?? $user->user_id,
                resetUrl: $resetUrl,
                identifier: $user->mobile ?? $user->email,
            ));
        } catch (Throwable $e) {
            return back()
                ->withErrors(['login' => 'The reset email could not be sent. Please try again later.'])
                ->withInput();
        }

        session([
            'student_password_reset_sent' => [
                'email'  => $this->maskEmail($user->email),
                'lookup' => $lookup,
            ],
        ]);

        return redirect()->route('student.password.sent');
    }

    public function showSentPage()
    {
        $payload = session('student_password_reset_sent');

        if (!$payload) {
            return redirect()->route('student.password.request');
        }

        return view('student.auth.forgot-password-sent', [
            'maskedEmail' => $payload['email'],
        ]);
    }

    public function showResetForm(Request $request, string $token)
    {
        $email = trim((string) $request->query('email'));

        if (!$email) {
            return redirect()->route('student.password.request')
                ->withErrors(['login' => 'This reset link is invalid. Please request a new one.']);
        }

        $record = DB::table('account_password_resets')
            ->where('account_type', 'user')
            ->where('email', $email)
            ->first();

        if (!$record || !$this->tokenIsValid($record, $token)) {
            return redirect()->route('student.password.request')
                ->withErrors(['login' => 'This reset link is invalid or has expired.']);
        }

        return view('student.auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $record = DB::table('account_password_resets')
            ->where('account_type', 'user')
            ->where('email', $data['email'])
            ->first();

        if (!$record || !$this->tokenIsValid($record, $data['token'])) {
            return back()->withErrors(['email' => 'This reset link is invalid or has expired.'])->withInput();
        }

        $user = User::where('email', $data['email'])->where('role', 'student')->first();

        if (!$user) {
            return redirect()->route('student.password.request')
                ->withErrors(['login' => 'No student account found. Please request a new reset link.']);
        }

        $user->password       = Hash::make($data['password']);
        $user->remember_token = Str::random(60);
        $user->save();

        DB::table('account_password_resets')
            ->where('account_type', 'user')
            ->where('email', $data['email'])
            ->delete();

        return redirect()->route('student.login')
            ->with('success', 'Password reset successfully. You can now sign in.');
    }

    private function findStudent(string $lookup): ?User
    {
        return User::where('role', 'student')
            ->where(function ($q) use ($lookup) {
                $q->where('email', $lookup)
                  ->orWhere('mobile', $lookup)
                  ->orWhere('user_id', $lookup);
            })
            ->first();
    }

    private function tokenIsValid(object $record, string $plainToken): bool
    {
        if (!Hash::check($plainToken, $record->token)) {
            return false;
        }

        return Carbon::parse($record->created_at)
            ->addMinutes(self::TOKEN_EXPIRY_MINUTES)
            ->isFuture();
    }

    private function maskEmail(string $email): string
    {
        [$name, $domain] = explode('@', $email, 2);

        $visible    = substr($name, 0, min(2, strlen($name)));
        $maskedName = $visible . str_repeat('*', max(strlen($name) - strlen($visible), 2));

        $domainParts  = explode('.', $domain);
        $domainName   = $domainParts[0] ?? '';
        $domainExt    = implode('.', array_slice($domainParts, 1));
        $maskedDomain = substr($domainName, 0, 1) . str_repeat('*', max(strlen($domainName) - 1, 2));

        return $maskedName . '@' . $maskedDomain . ($domainExt ? '.' . $domainExt : '');
    }
}
