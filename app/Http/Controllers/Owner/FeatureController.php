<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::latest()->get();
        return view('owner.features.index', compact('features'));
    }

    public function create()
    {
        return view('owner.features.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'status'      => 'required|in:active,inactive',
        ]);
        $data['slug'] = Str::slug($data['name'], '_');

        Feature::create($data);
        return redirect()->route('owner.features.index')->with('success', 'Feature added successfully.');
    }

    public function edit(Feature $feature)
    {
        return view('owner.features.edit', compact('feature'));
    }

    public function update(Request $request, Feature $feature)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'status'      => 'required|in:active,inactive',
        ]);
        $feature->update($data);
        return redirect()->route('owner.features.index')->with('success', 'Feature updated.');
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();
        return redirect()->route('owner.features.index')->with('success', 'Feature deleted.');
    }

    public function toggle(Feature $feature)
    {
        $feature->update(['status' => $feature->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', 'Status updated.');
    }
}
