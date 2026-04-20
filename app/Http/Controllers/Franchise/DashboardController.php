<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FranchiseTransaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('institute')->user();
        $franchise = $user->franchise()->with('wallet')->firstOrFail();

        $recentTransactions = FranchiseTransaction::where('franchise_id', $franchise->id)
            ->latest()
            ->limit(8)
            ->get();

        return view('franchise.dashboard', compact('franchise', 'recentTransactions'));
    }
}
