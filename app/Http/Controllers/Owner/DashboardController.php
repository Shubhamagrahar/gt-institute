<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner\{Feature, Plan, Institute, InstituteTransaction};

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_institutes'  => Institute::count(),
            'active_institutes' => Institute::where('status', 'active')->count(),
            'total_plans'       => Plan::count(),
            'total_features'    => Feature::count(),
            'recent_institutes' => Institute::with('subscription.plan')->latest()->take(5)->get(),
            'recent_txns'       => InstituteTransaction::with('institute')->latest()->take(10)->get(),
        ];

        // Monthly institute signups (last 6 months)
        $monthlySignups = Institute::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return view('owner.dashboard', compact('stats', 'monthlySignups'));
    }
}
