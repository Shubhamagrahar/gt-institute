<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\BatchDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BatchController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index()
    {
        $batches = BatchDetail::where('institute_id', $this->instituteId())
            ->latest()
            ->get();

        return view('institute.batches.index', compact('batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'status' => 'required|in:active,inactive',
        ]);

        BatchDetail::create(array_merge($data, [
            'institute_id' => $this->instituteId(),
        ]));

        return back()->with('success', 'Batch created successfully.');
    }

    public function toggle(BatchDetail $batch)
    {
        $this->authorizeBatch($batch);

        $batch->update([
            'status' => $batch->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Batch status updated.');
    }

    public function destroy(BatchDetail $batch)
    {
        $this->authorizeBatch($batch);
        $batch->delete();

        return back()->with('success', 'Batch deleted successfully.');
    }

    private function authorizeBatch(BatchDetail $batch): void
    {
        abort_unless((int) $batch->institute_id === $this->instituteId(), 403);
    }
}
