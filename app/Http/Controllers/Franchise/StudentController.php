<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\{CourseBook, FeeCollectDetail};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    private function franchiseId(): int
    {
        return Auth::guard('institute')->user()->franchise_id;
    }

    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index(Request $request)
    {
        $fid = $this->franchiseId();
        $amountColumn = FeeCollectDetail::amountColumn();

        $query = CourseBook::with(['student.profile', 'course', 'batch', 'paymentPlan'])
            ->where('franchise_id', $fid)
            ->whereIn('status', ['OPEN', 'RUN']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('mobile', 'like', "%$search%")
                  ->orWhere('user_id', 'like', "%$search%")
                  ->orWhereHas('profile', fn ($p) => $p->where('name', 'like', "%$search%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->orderByDesc('id')->paginate(20)->withQueryString();

        // Compute paid_amount per course book
        $students->each(function (CourseBook $cb) use ($amountColumn) {
            $paid = (float) FeeCollectDetail::where('course_book_id', $cb->id)
                ->whereNull('cancelled_at')->sum($amountColumn);
            $cb->setAttribute('paid_amount', $paid);
        });

        $totalStudents  = CourseBook::where('franchise_id', $fid)->whereIn('status', ['OPEN', 'RUN'])->count();
        $admittedCount  = CourseBook::where('franchise_id', $fid)->where('status', 'RUN')->count();
        $pendingCount   = CourseBook::where('franchise_id', $fid)->where('status', 'OPEN')->count();

        return view('franchise.students.index', compact(
            'students', 'totalStudents', 'admittedCount', 'pendingCount'
        ));
    }
}
