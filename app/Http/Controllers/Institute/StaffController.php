<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\{StaffProfile, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash};

class StaffController extends Controller
{
    private function institute() { return Auth::guard('institute')->user()->institute; }

    public function index()
    {
        $staff = User::with('staffProfile')
            ->where('institute_id', $this->institute()->id)
            ->where('role', 'staff')->latest()->paginate(20);
        return view('institute.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('institute.staff.create');
    }

    public function store(Request $request)
    {
        $institute = $this->institute();
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'mobile'      => 'required|string|max:15|unique:users,mobile',
            'email'       => 'nullable|email|max:100',
            'designation' => 'nullable|string|max:80',
            'salary'      => 'nullable|numeric|min:0',
            'joining_date'=> 'nullable|date',
        ]);

        $plain = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ0123456789'), 0, 8));

        DB::transaction(function () use ($data, $institute, $plain) {
            $count = User::where('institute_id', $institute->id)->where('role', 'staff')->count();
            $userId = ($institute->short_name ?? 'STF') . '/STAFF/' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

            $user = User::create([
                'user_id'      => strtoupper($userId),
                'name'         => $data['name'],
                'mobile'       => $data['mobile'],
                'email'        => $data['email'] ?? null,
                'password'     => Hash::make($plain),
                'role'         => 'staff',
                'institute_id' => $institute->id,
                'status'       => 'active',
            ]);

            StaffProfile::create([
                'user_id'      => $user->id,
                'institute_id' => $institute->id,
                'designation'  => $data['designation'] ?? null,
                'salary'       => $data['salary'] ?? 0,
                'joining_date' => $data['joining_date'] ?? null,
            ]);
        });

        return redirect()->route('institute.staff.index')->with('success', 'Staff member added.');
    }

    public function show(User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id, 403);
        $staff->load('staffProfile');
        return view('institute.staff.show', compact('staff'));
    }

    public function edit(User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id, 403);
        $staff->load('staffProfile');
        return view('institute.staff.edit', compact('staff'));
    }

    public function update(Request $request, User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id, 403);
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'designation' => 'nullable|string|max:80',
            'salary'      => 'nullable|numeric|min:0',
            'joining_date'=> 'nullable|date',
        ]);
        $staff->update(['name' => $data['name']]);
        $staff->staffProfile?->update($data);
        return redirect()->route('institute.staff.show', $staff)->with('success', 'Staff updated.');
    }

    public function destroy(User $staff)
    {
        abort_unless($staff->institute_id === $this->institute()->id, 403);
        $staff->delete();
        return redirect()->route('institute.staff.index')->with('success', 'Staff removed.');
    }
}
