<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetLinkMail;
use App\Models\SuperAdmin;
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
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'login' => 'required|string|max:100',
        ]);

        $lookup = trim((string) $request->input('login'));
        $account = $this->findAccount($lookup);

        if (!$account) {
            return back()
                ->withErrors(['login' => 'No account was found for that email, mobile number, or application number.'])
                ->withInput();
        }

        if (($account['model']->status ?? 'active') !== 'active') {
            return back()
                ->withErrors(['login' => 'Your account is inactive. Please contact support or your institute administrator.'])
                ->withInput();
        }

        if (!$account['email']) {
            return back()
                ->withErrors(['login' => 'No email address is available for this account. Please contact support or your institute administrator.'])
                ->withInput();
        }

        $plainToken = Str::random(64);

        DB::table('account_password_resets')->updateOrInsert(
            [
                'account_type' => $account['type'],
                'email' => $account['email'],
            ],
            [
                'token' => Hash::make($plainToken),
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $plainToken]) . '?' . http_build_query([
            'email' => $account['email'],
            'type' => $account['type'],
        ]);

        try {
            Mail::to($account['email'])->send(new PasswordResetLinkMail(
                accountName: $this->resolveDisplayName($account['model']),
                resetUrl: $resetUrl,
                identifier: $account['identifier']
            ));
        } catch (Throwable $e) {
            return back()
                ->withErrors(['login' => 'The reset email could not be sent. Please check the mail settings or try again in a moment.'])
                ->withInput();
        }

        session([
            'password_reset_sent' => [
                'email' => $this->maskEmail($account['email']),
                'lookup' => $lookup,
            ],
        ]);

        return redirect()->route('password.sent');
    }

    public function showSentPage()
    {
        $payload = session('password_reset_sent');

        if (!$payload) {
            return redirect()->route('password.request');
        }

        return view('auth.forgot-password-sent', [
            'maskedEmail' => $payload['email'],
        ]);
    }

    public function showResetForm(Request $request, string $token)
    {
        $email = trim((string) $request->query('email'));
        $type = trim((string) $request->query('type'));

        if (!$email || !in_array($type, ['super_admin', 'user'], true)) {
            return redirect()->route('password.request')
                ->withErrors(['login' => 'This reset link is invalid. Please request a new one.']);
        }

        $record = DB::table('account_password_resets')
            ->where('account_type', $type)
            ->where('email', $email)
            ->first();

        if (!$record || !$this->tokenIsValid($record, $token)) {
            return redirect()->route('password.request')
                ->withErrors(['login' => 'This reset link is invalid or has expired.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
            'type' => $type,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'type' => 'required|in:super_admin,user',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $record = DB::table('account_password_resets')
            ->where('account_type', $data['type'])
            ->where('email', $data['email'])
            ->first();

        if (!$record || !$this->tokenIsValid($record, $data['token'])) {
            return back()->withErrors(['email' => 'This reset link is invalid or has expired.'])->withInput();
        }

        $account = $data['type'] === 'super_admin'
            ? SuperAdmin::where('email', $data['email'])->first()
            : User::where('email', $data['email'])->first();

        if (!$account) {
            return redirect()->route('password.request')
                ->withErrors(['login' => 'We could not find that account. Please request a new reset link.']);
        }

        $account->password = Hash::make($data['password']);
        $account->remember_token = Str::random(60);
        $account->save();

        DB::table('account_password_resets')
            ->where('account_type', $data['type'])
            ->where('email', $data['email'])
            ->delete();

        return redirect()->route('login')->with('success', 'Your password has been reset successfully. You can now sign in.');
    }

    private function findAccount(string $lookup): ?array
    {
        $admin = SuperAdmin::query()
            ->where('email', $lookup)
            ->orWhere('mobile', $lookup)
            ->orWhere('admin_id', $lookup)
            ->first();

        if ($admin) {
            return [
                'type' => 'super_admin',
                'model' => $admin,
                'email' => $admin->email,
                'identifier' => $admin->admin_id ?: $admin->mobile,
            ];
        }

        $user = User::query()
            ->where('email', $lookup)
            ->orWhere('mobile', $lookup)
            ->orWhere('user_id', $lookup)
            ->first();

        if ($user) {
            return [
                'type' => 'user',
                'model' => $user,
                'email' => $user->email,
                'identifier' => $user->user_id ?: $user->mobile,
            ];
        }

        return null;
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

        $visible = substr($name, 0, min(2, strlen($name)));
        $maskedName = $visible . str_repeat('*', max(strlen($name) - strlen($visible), 2));

        $domainParts = explode('.', $domain);
        $domainName = $domainParts[0] ?? '';
        $domainExt = implode('.', array_slice($domainParts, 1));
        $maskedDomain = substr($domainName, 0, 1) . str_repeat('*', max(strlen($domainName) - 1, 2));

        return $maskedName . '@' . $maskedDomain . ($domainExt ? '.' . $domainExt : '');
    }

    private function resolveDisplayName(object $account): string
    {
        if ($account instanceof SuperAdmin) {
            return $account->name;
        }

        return $account->profile?->name ?? $account->user_id;
    }
}
