<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\InstituteSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index()
    {
        $sessions = InstituteSession::where('institute_id', $this->instituteId())
            ->orderByDesc('is_active')
            ->orderByDesc('start_date')
            ->get();

        return view('institute.sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('institute.sessions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $data['institute_id'] = $this->instituteId();
        $data['is_active']    = false;

        $noSessionExists = InstituteSession::where('institute_id', $this->instituteId())->count() === 0;

        $session = InstituteSession::create($data);

        // Pehla session hai to auto-activate karo
        if ($noSessionExists) {
            $session->activate();
        }

        return redirect()->route('institute.sessions.index')
            ->with('success', 'Session created successfully.' . ($noSessionExists ? ' Auto-activated as first session.' : ''));
    }

    // Toggle active/inactive
    public function toggle(InstituteSession $session)
    {
        if ($session->institute_id !== $this->instituteId()) abort(403);

        if ($session->is_active) {
            // Already active — deactivate mat karo, ek active rehna chahiye
            return back()->with('error', 'At least one session must remain active. Activate another session first.');
        }

        $session->activate();

        return back()->with('success', "Session \"{$session->name}\" is now active.");
    }

    public function destroy(InstituteSession $session)
    {
        if ($session->institute_id !== $this->instituteId()) abort(403);

        if ($session->is_active) {
            return back()->with('error', 'Cannot delete active session. Activate another session first.');
        }

        $session->delete();
        return back()->with('success', 'Session deleted.');
    }

    public function switch(Request $request)
{
    $request->validate(['session_id' => 'required|exists:institute_sessions,id']);

    $session = InstituteSession::where('id', $request->session_id)
        ->where('institute_id', $this->instituteId())
        ->firstOrFail();

    // Laravel session mein selected session store karo (DB change nahi hoga)
    session(['selected_session_id' => $session->id]);

    return back()->with('success', "Viewing data for: {$session->name}");
}
}