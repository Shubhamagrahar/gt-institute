<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\CourseFeeStructure;
use App\Models\FeeType;
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
            ->with('courseType')
            ->orderBy('course_type_id')
            ->orderBy('course_name')
            ->get();

        return view('franchise.pricing.index', compact('franchise', 'charges'));
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

    // ── Fee Bindings (franchise picks from institute fee types) ─────────────

    public function feeBindingsEdit(FranchiseCourseCharge $charge)
    {
        abort_if($charge->franchise_id !== $this->franchiseId(), 403);

        $iid = $this->instituteId();
        $fid = $this->franchiseId();

        // Institute ke fee types — franchise sirf inhein use kar sakta hai
        $instFeeTypes = FeeType::where('institute_id', $iid)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Franchise ke existing bindings for this course
        $myBindings = FranchiseFeeStructure::where('franchise_id', $fid)
            ->where('course_id', $charge->course_id)
            ->with('feeType')
            ->orderBy('sort_order')
            ->get();

        return view('franchise.pricing.fee-bindings-edit', compact('charge', 'instFeeTypes', 'myBindings'));
    }

    public function feeBindingsSave(Request $request, FranchiseCourseCharge $charge)
    {
        abort_if($charge->franchise_id !== $this->franchiseId(), 403);

        $iid = $this->instituteId();
        $fid = $this->franchiseId();

        $data = $request->validate([
            'fee_type_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('fee_types', 'id')
                    ->where(fn ($q) => $q->where('institute_id', $iid)->where('is_active', true)),
            ],
            'amount' => 'required|numeric|min:0.01|max:99999',
        ]);

        $feeType = FeeType::where('institute_id', $iid)->findOrFail($data['fee_type_id']);

        // Each fee type can be bound only once per course per franchise
        $already = FranchiseFeeStructure::where('franchise_id', $fid)
            ->where('course_id', $charge->course_id)
            ->where('fee_type_id', $feeType->id)
            ->exists();

        if ($already) {
            return back()->with('error', '"' . $feeType->name . '" is already bound to this course. Edit or remove the existing binding.');
        }

        $maxOrder = FranchiseFeeStructure::where('franchise_id', $fid)
            ->where('course_id', $charge->course_id)
            ->max('sort_order') ?? 0;

        FranchiseFeeStructure::create([
            'franchise_id'  => $fid,
            'institute_id'  => $iid,
            'course_id'     => $charge->course_id,
            'fee_type_id'   => $feeType->id,
            'fee_type_name' => $feeType->name,
            'amount'        => round((float) $data['amount'], 2),
            'enabled'       => true,
            'sort_order'    => $maxOrder + 1,
        ]);

        return redirect()->route('franchise.pricing.fee-bindings.edit', $charge)
            ->with('success', '"' . $feeType->name . '" (₹' . number_format($data['amount'], 2) . ') bound successfully.');
    }

    public function feeBindingsRemove(FranchiseFeeStructure $binding)
    {
        abort_if($binding->franchise_id !== $this->franchiseId(), 403);
        $binding->delete();
        return back()->with('success', 'Fee binding removed.');
    }

    // Save fee structures for a course (toggle + amounts)
    public function saveFeeStructures(Request $request, FranchiseCourseCharge $charge)
    {
        $fid = $this->franchiseId();
        $iid = $this->instituteId();
        abort_if($charge->franchise_id !== $fid, 403);

        // Read raw fees array directly from request (bypasses boolean-cast issue)
        $rawFees  = $request->input('fees', []);
        $incoming = collect(is_array($rawFees) ? $rawFees : []);

        \DB::transaction(function () use ($incoming, $fid, $iid, $charge) {
            FranchiseFeeStructure::where('franchise_id', $fid)
                ->where('course_id', $charge->course_id)
                ->delete();

            foreach ($incoming as $idx => $row) {
                // Unchecked checkboxes send no key at all; treat absent/falsy as disabled
                if (!isset($row['enabled']) || !$row['enabled']) {
                    continue;
                }

                $name = trim((string) ($row['fee_type_name'] ?? ''));
                if ($name === '') continue;

                FranchiseFeeStructure::create([
                    'franchise_id'  => $fid,
                    'institute_id'  => $iid,
                    'course_id'     => $charge->course_id,
                    'fee_type_id'   => isset($row['fee_type_id']) && $row['fee_type_id'] !== '' ? (int) $row['fee_type_id'] : null,
                    'fee_type_name' => $name,
                    'amount'        => (float) ($row['amount'] ?? 0),
                    'enabled'       => true,
                    'sort_order'    => (int) ($row['sort_order'] ?? $idx),
                ]);
            }
        });

        return redirect()->route('franchise.pricing.index')
            ->with('success', 'Fee types saved for "' . $charge->course_name . '".');
    }
}
