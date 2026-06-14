<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\{Franchise, FranchiseTransaction};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    private function franchise(): Franchise
    {
        $user = Auth::guard('institute')->user();
        return Franchise::with(['wallet', 'institute', 'level'])
            ->where('id', $user->franchise_id)
            ->where('institute_id', $user->institute_id)
            ->firstOrFail();
    }

    public function index(Request $request)
    {
        $franchise = $this->franchise();

        $query = FranchiseTransaction::where('franchise_id', $franchise->id)
            ->latest('date');

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }
        if ($request->filled('type')) {
            if ($request->type === 'credit') $query->where('credit', '>', 0);
            if ($request->type === 'debit')  $query->where('debit', '>', 0);
        }

        $transactions = $query->paginate(25)->withQueryString();

        return view('franchise.wallet.index', compact('franchise', 'transactions'));
    }
}
