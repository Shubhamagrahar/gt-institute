<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StaffRole;

class StaffRoleController extends Controller
{
    private function institute() { return Auth::guard('institute')->user()->institute; }

    public function index()
    {
        $roles = StaffRole::where('institute_id', $this->institute()->id)
            ->withCount('staff')
            ->orderBy('name')
            ->get();

        return view('institute.staff-roles.index', compact('roles'));
    }

    public function create()
    {
        return view('institute.staff-roles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:80',
            'short_code'  => ['required', 'string', 'max:5', 'regex:/^[A-Z]+$/'],
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:300',
            'grace_days'  => 'nullable|integer|min:0|max:31',
        ], [
            'short_code.regex' => 'Short code must contain only uppercase letters.',
        ]);

        StaffRole::create([
            'institute_id' => $this->institute()->id,
            'name'         => $data['name'],
            'short_code'   => strtoupper($data['short_code']),
            'color'        => $data['color'] ?? '#6c5dd3',
            'description'  => $data['description'] ?? null,
            'grace_days'   => $data['grace_days'] ?? 2,
            'permissions'  => [],
            'status'       => 'active',
        ]);

        return redirect()->route('institute.staff-roles.index')
            ->with('success', 'Role "' . $data['name'] . '" created.');
    }

    public function edit(StaffRole $staffRole)
    {
        abort_unless($staffRole->institute_id === $this->institute()->id, 403);
        $staffRole->loadCount('staff');
        return view('institute.staff-roles.edit', compact('staffRole'));
    }

    public function update(Request $request, StaffRole $staffRole)
    {
        abort_unless($staffRole->institute_id === $this->institute()->id, 403);

        $data = $request->validate([
            'name'        => 'required|string|max:80',
            'short_code'  => ['required', 'string', 'max:5', 'regex:/^[A-Z]+$/'],
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:300',
            'grace_days'  => 'nullable|integer|min:0|max:31',
            'status'      => 'required|in:active,inactive',
        ], [
            'short_code.regex' => 'Short code must contain only uppercase letters.',
        ]);

        $staffRole->update([
            'name'        => $data['name'],
            'short_code'  => strtoupper($data['short_code']),
            'color'       => $data['color'] ?? $staffRole->color,
            'description' => $data['description'] ?? null,
            'grace_days'  => $data['grace_days'] ?? $staffRole->grace_days,
            'status'      => $data['status'],
        ]);

        return redirect()->route('institute.staff-roles.index')
            ->with('success', 'Role updated.');
    }

    public function destroy(StaffRole $staffRole)
    {
        abort_unless($staffRole->institute_id === $this->institute()->id, 403);

        $count = $staffRole->staff()->count();
        if ($count > 0) {
            return back()->with('error', "Cannot delete — {$count} staff member(s) use this role.");
        }

        $staffRole->delete();
        return redirect()->route('institute.staff-roles.index')
            ->with('success', 'Role deleted.');
    }
}
