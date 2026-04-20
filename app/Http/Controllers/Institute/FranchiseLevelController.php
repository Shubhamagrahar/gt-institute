<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\FranchiseLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FranchiseLevelController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index()
    {
        $levels = FranchiseLevel::where('institute_id', $this->instituteId())
            ->latest()
            ->get();

        return view('institute.franchise-levels.index', compact('levels'));
    }

    public function create()
    {
        return view('institute.franchise-levels.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $data['institute_id'] = $this->instituteId();
        FranchiseLevel::create($data);

        return redirect()->route('institute.franchise-levels.index')->with('success', 'Franchise level created successfully.');
    }

    public function edit(FranchiseLevel $franchiseLevel)
    {
        $this->authorizeLevel($franchiseLevel);

        return view('institute.franchise-levels.edit', ['level' => $franchiseLevel]);
    }

    public function update(Request $request, FranchiseLevel $franchiseLevel)
    {
        $this->authorizeLevel($franchiseLevel);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $franchiseLevel->update($data);

        return redirect()->route('institute.franchise-levels.index')->with('success', 'Franchise level updated successfully.');
    }

    private function authorizeLevel(FranchiseLevel $franchiseLevel): void
    {
        abort_if($franchiseLevel->institute_id !== $this->instituteId(), 403);
    }
}
