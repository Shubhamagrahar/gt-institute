<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\BatchDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BatchController extends Controller
{
    private function franchiseUser()
    {
        return Auth::guard('institute')->user();
    }

    private function franchiseId(): int
    {
        return $this->franchiseUser()->franchise_id;
    }

    private function instituteId(): int
    {
        return $this->franchiseUser()->institute_id;
    }

    public function index()
    {
        $batches = BatchDetail::forFranchise($this->franchiseId())
            ->latest()
            ->get();

        return view('franchise.batches.index', compact('batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'start_time' => 'nullable|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i|after:start_time',
            'status'     => 'required|in:active,inactive',
        ]);

        BatchDetail::create(array_merge($data, [
            'institute_id' => $this->instituteId(),
            'franchise_id' => $this->franchiseId(),
        ]));

        return back()->with('success', 'Batch created successfully.');
    }

    public function toggle(BatchDetail $batch)
    {
        $this->authorize($batch);
        $batch->update(['status' => $batch->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', 'Batch status updated.');
    }

    public function destroy(BatchDetail $batch)
    {
        $this->authorize($batch);
        $batch->delete();
        return back()->with('success', 'Batch deleted.');
    }

    private function authorize(BatchDetail $batch): void
    {
        abort_unless((int) $batch->franchise_id === $this->franchiseId(), 403);
    }
}
