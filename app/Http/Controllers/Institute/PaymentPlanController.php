<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlanType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentPlanController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index()
    {
        $plans = PaymentPlanType::where('institute_id', $this->instituteId())->latest()->get();
        return view('institute.payment-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('institute.payment-plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'type'             => 'required|in:OTP,MONTHLY,PART',
            'grace_days'       => 'required|integer|min:0',
            'late_fee_per_day' => 'required|numeric|min:0',
        ]);

        PaymentPlanType::create(array_merge($data, [
            'institute_id' => $this->instituteId(),
            'is_active'    => true,
        ]));

        return redirect()->route('institute.payment-plans.index')
            ->with('success', 'Payment plan created.');
    }

    public function edit(PaymentPlanType $paymentPlan)
    {
        $this->authorize($paymentPlan);
        return view('institute.payment-plans.edit', compact('paymentPlan'));
    }

    public function update(Request $request, PaymentPlanType $paymentPlan)
    {
        $this->authorize($paymentPlan);
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'type'             => 'required|in:OTP,MONTHLY,PART',
            'grace_days'       => 'required|integer|min:0',
            'late_fee_per_day' => 'required|numeric|min:0',
        ]);
        $paymentPlan->update($data);
        return redirect()->route('institute.payment-plans.index')->with('success', 'Updated.');
    }

    public function destroy(PaymentPlanType $paymentPlan)
    {
        $this->authorize($paymentPlan);
        $paymentPlan->delete();
        return back()->with('success', 'Deleted.');
    }

    private function authorize(PaymentPlanType $plan): void
    {
        if ($plan->institute_id !== $this->instituteId()) abort(403);
    }
}