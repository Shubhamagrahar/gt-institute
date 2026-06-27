<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        // Owner panel
        if (in_array('owner', $roles)) {
            if (!Auth::guard('web')->check()) {
                return redirect()->route('owner.login');
            }
            $admin = Auth::guard('web')->user();
            if ($admin->status === 'inactive') {
                Auth::guard('web')->logout();
                return redirect()->route('owner.login')->withErrors(['login' => 'Account deactivated.']);
            }
            return $next($request);
        }

        // Institute panel
        if (!Auth::guard('institute')->check()) {
            return redirect()->route('login');
        }
        $user = Auth::guard('institute')->user();
        if ($user->status === 'inactive') {
            Auth::guard('institute')->logout();
            return redirect()->route('login')->withErrors(['login' => 'Account deactivated.']);
        }
        if (!in_array($user->role, $roles)) {
            // Redirect to correct portal — don't 403 a validly-authenticated user
            return match (true) {
                in_array($user->role, ['franchise_head', 'franchise_staff', 'franchise_student'])
                    => redirect()->route('franchise.dashboard'),
                default => redirect()->route('login'),
            };
        }
        return $next($request);
    }
}