<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeTypeController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index()
    {
        $feeTypes = FeeType::where('institute_id', $this->instituteId())->latest()->get();
        return view('institute.fee-types.index', compact('feeTypes'));
    }

    public function create()
    {
        return view('institute.fee-types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'is_mandatory' => 'sometimes|boolean',
        ]);

        FeeType::create([
            'institute_id' => $this->instituteId(),
            'name'         => $data['name'],
            'is_mandatory' => $request->boolean('is_mandatory'),
            'is_active'    => true,
        ]);

        return redirect()->route('institute.fee-types.index')
            ->with('success', 'Fee type created.');
    }

    public function edit(FeeType $feeType)
    {
        $this->authorize($feeType);
        return view('institute.fee-types.edit', compact('feeType'));
    }

    public function update(Request $request, FeeType $feeType)
    {
        $this->authorize($feeType);
        $data = $request->validate(['name' => 'required|string|max:100']);
        $feeType->update([
            'name'         => $data['name'],
            'is_mandatory' => $request->boolean('is_mandatory'),
        ]);
        return redirect()->route('institute.fee-types.index')->with('success', 'Updated.');
    }

    public function destroy(FeeType $feeType)
    {
        $this->authorize($feeType);
        $feeType->delete();
        return back()->with('success', 'Fee type deleted.');
    }

    private function authorize(FeeType $feeType): void
    {
        if ($feeType->institute_id !== $this->instituteId()) abort(403);
    }
}