<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStaffPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = Auth::guard('staff')->user();

        if (!$user || !$user->hasStaffPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Permission denied.'], 403);
            }
            return redirect()->route('staff.dashboard')
                ->with('error', 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
