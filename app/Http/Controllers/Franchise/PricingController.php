<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\FranchiseCourseCharge;
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

    public function index()
    {
        $franchise = $this->franchise();

        $charges = FranchiseCourseCharge::where('franchise_id', $franchise->id)
            ->where('enabled', true)
            ->with('courseType', 'course')
            ->orderBy('course_type_id')
            ->orderBy('course_name')
            ->get();

        return view('franchise.pricing.index', compact('franchise', 'charges'));
    }

    public function update(Request $request, FranchiseCourseCharge $charge)
    {
        abort_if($charge->franchise_id !== $this->franchiseId(), 403);

        $cap = (float) ($charge->course?->fee ?? PHP_INT_MAX);

        $data = $request->validate([
            'student_fee' => ['required', 'numeric', 'min:0', "max:{$cap}"],
        ]);

        $charge->update(['student_fee' => $data['student_fee']]);

        return back()->with('success', 'Student fee for "' . $charge->course_name . '" updated to Rs.' . number_format($data['student_fee'], 2) . '.');
    }
}
