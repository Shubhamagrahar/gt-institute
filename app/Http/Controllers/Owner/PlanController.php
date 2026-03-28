<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner\{Feature, Plan};
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('features')->latest()->get();
        return view('owner.plans.index', compact('plans'));
    }

    public function create()
    {
        $features = Feature::where('status', 'active')->get();
        return view('owner.plans.create', compact('features'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'price'       => 'required|numeric|min:0',
            'duration'    => 'required|integer|min:1',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
            'features'    => 'nullable|array',
            'features.*'  => 'exists:features,id',
        ]);

        $plan = Plan::create($data);
        if (!empty($data['features'])) {
            $plan->features()->sync($data['features']);
        }

        return redirect()->route('owner.plans.index')->with('success', 'Plan created successfully.');
    }

    public function show(Plan $plan)
    {
        $plan->load('features');
        return view('owner.plans.show', compact('plan'));
    }

    public function edit(Plan $plan)
    {
        $features    = Feature::where('status', 'active')->get();
        $planFeatures = $plan->features->pluck('id')->toArray();
        return view('owner.plans.edit', compact('plan', 'features', 'planFeatures'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'price'       => 'required|numeric|min:0',
            'duration'    => 'required|integer|min:1',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
            'features'    => 'nullable|array',
            'features.*'  => 'exists:features,id',
        ]);

        $plan->update($data);
        $plan->features()->sync($data['features'] ?? []);

        return redirect()->route('owner.plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('owner.plans.index')->with('success', 'Plan deleted.');
    }

    public function toggle(Plan $plan)
    {
        $plan->update(['status' => $plan->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', 'Status updated.');
    }
}
