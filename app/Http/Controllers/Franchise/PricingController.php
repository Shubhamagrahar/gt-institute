<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\CourseFeeStructure;
use App\Models\Franchise;
use App\Models\FranchiseCourseCharge;
use App\Models\FranchiseFeeStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PricingController extends Controller
{
    private function franchise(): Franchise
    {
        return Auth::guard('institute')->user()
            ->franchise()
            ->with('level')
            ->firstOrFail();
    }

    private function franchiseId(): int
    {
        return Auth::guard('institute')->user()->franchise_id;
    }

    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index()
    {
        $franchise = $this->franchise();
        $fid       = $franchise->id;
        $iid       = $franchise->institute_id;

        $charges = FranchiseCourseCharge::where('franchise_id', $fid)
            ->where('enabled', true)
            ->with('courseType', 'course.feeStructures.feeType')
            ->orderBy('course_type_id')
            ->orderBy('course_name')
            ->get();

        // Load all franchise fee structures keyed by course_id
        $feeStructures = FranchiseFeeStructure::where('franchise_id', $fid)
            ->get()
            ->groupBy('course_id');

        return view('franchise.pricing.index', compact('franchise', 'charges', 'feeStructures'));
    }

    // Update student_fee for a course
    public function update(Request $request, FranchiseCourseCharge $charge)
    {
        abort_if($charge->franchise_id !== $this->franchiseId(), 403);

        $cap  = (float) ($charge->course?->fee ?? PHP_INT_MAX);
        $data = $request->validate([
            'student_fee' => ['required', 'numeric', 'min:0', "max:{$cap}"],
        ]);

        $charge->update(['student_fee' => $data['student_fee']]);

        return back()->with('success', 'Student fee for "' . $charge->course_name . '" updated to Rs.' . number_format($data['student_fee'], 2) . '.');
    }

    // Save fee structures for a course (toggle + amounts)
    public function saveFeeStructures(Request $request, FranchiseCourseCharge $charge)
    {
        $fid = $this->franchiseId();
        $iid = $this->instituteId();
        abort_if($charge->franchise_id !== $fid, 403);

        $data = $request->validate([
            'fees'              => 'nullable|array',
            'fees.*.fee_type_id'   => 'nullable|integer',
            'fees.*.fee_type_name' => 'required_with:fees.*|string|max:100',
            'fees.*.amount'        => 'required_with:fees.*|numeric|min:0',
            'fees.*.enabled'       => 'nullable|boolean',
            'fees.*.sort_order'    => 'nullable|integer',
        ]);

        $incoming = collect($data['fees'] ?? []);

        \DB::transaction(function () use ($incoming, $fid, $iid, $charge) {
            FranchiseFeeStructure::where('franchise_id', $fid)
                ->where('course_id', $charge->course_id)
                ->delete();

            foreach ($incoming as $row) {
                // checkbox only submits when checked — key absent means unchecked
                if (empty($row['enabled'])) continue;

                FranchiseFeeStructure::create([
                    'franchise_id'  => $fid,
                    'institute_id'  => $iid,
                    'course_id'     => $charge->course_id,
                    'fee_type_id'   => $row['fee_type_id'] ?? null,
                    'fee_type_name' => $row['fee_type_name'],
                    'amount'        => $row['amount'],
                    'enabled'       => true,
                    'sort_order'    => $row['sort_order'] ?? 0,
                ]);
            }
        });

        return back()->with('success', 'Additional fees updated for "' . $charge->course_name . '".');
    }
}
