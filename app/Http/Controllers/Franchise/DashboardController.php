<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\CourseBook;
use App\Models\FranchiseTransaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user      = Auth::guard('institute')->user();
        $franchise = $user->franchise()->with('wallet', 'level', 'institute')->firstOrFail();
        $fid       = $franchise->id;

        $recentTransactions = FranchiseTransaction::where('franchise_id', $fid)
            ->latest()->limit(8)->get();

        $totalStudents  = CourseBook::where('franchise_id', $fid)->whereIn('status', ['OPEN', 'RUN'])->count();
        $runningCount   = CourseBook::where('franchise_id', $fid)->where('status', 'RUN')->count();
        $pendingCount   = CourseBook::where('franchise_id', $fid)->where('status', 'OPEN')->count();
        $recentStudents = CourseBook::with(['student.profile', 'course'])
            ->where('franchise_id', $fid)
            ->whereIn('status', ['OPEN', 'RUN'])
            ->latest()->limit(6)->get();

        return view('franchise.dashboard', compact(
            'franchise', 'recentTransactions',
            'totalStudents', 'runningCount', 'pendingCount', 'recentStudents'
        ));
    }
}
