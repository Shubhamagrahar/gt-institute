<?php

namespace App\Http\Middleware;

use App\Models\InstituteSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSession
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::guard('institute')->user();

        if (!$user) return $next($request);

        // Session create page pe mat rokna — infinite loop banega
        $allowedRoutes = [
            'institute.sessions.index',
            'institute.sessions.create',
            'institute.sessions.store',
            'logout',
        ];

        if (in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }

        // Check active session
        $hasSession = InstituteSession::where('institute_id', $user->institute_id)
            ->where('is_active', true)
            ->exists();

        if (!$hasSession) {
            return redirect()->route('institute.sessions.create')
                ->with('error', 'Please create and activate a session before proceeding.');
        }

        return $next($request);
    }
}