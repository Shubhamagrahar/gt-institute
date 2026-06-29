<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\{InstituteStudentTransaction, InstituteStudentWallet, StudentTransaction, StudentWallet, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

class WalletAdjustmentController extends Controller
{
    private function instituteId(): int
    {
        return Auth::guard('institute')->user()->institute_id;
    }

    public function index()
    {
        return view('institute.wallet-adjustment.index');
    }

    public function search(Request $request)
    {
        $q   = trim($request->get('q', ''));
        $iid = $this->instituteId();

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $students = User::where('institute_id', $iid)
            ->where('role', 'student')
            ->with(['profile', 'studentWallet', 'enrollments' => fn($eq) => $eq->whereIn('status', ['OPEN', 'RUN'])->limit(1)])
            ->where(function ($query) use ($q) {
                $query->where('user_id', 'like', "%{$q}%")
                      ->orWhere('mobile', 'like', "%{$q}%")
                      ->orWhereHas('profile', fn($pq) => $pq->where('name', 'like', "%{$q}%"))
                      ->orWhereHas('enrollments', fn($eq) => $eq->where('enrollment_no', 'like', "%{$q}%"));
            })
            ->limit(10)
            ->get();

        return response()->json($students->map(fn($s) => [
            'id'          => $s->id,
            'user_id'     => $s->user_id,
            'name'        => $s->profile?->name ?? $s->user_id,
            'mobile'      => $s->mobile,
            'balance'     => (float) ($s->studentWallet?->balance ?? 0),
            'enrollment'  => $s->enrollments->first()?->enrollment_no,
            'course'      => $s->enrollments->first()?->course?->name,
        ]));
    }

    public function credit(Request $request, User $user)
    {
        if ($user->institute_id !== $this->instituteId()) {
            abort(403);
        }

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:500000',
            'note'   => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($data, $user) {
            $iid    = $this->instituteId();
            $amount = (float) $data['amount'];
            $byUser = Auth::guard('institute')->id();

            $sw = StudentWallet::firstOrCreate(
                ['user_id' => $user->id],
                ['institute_id' => $iid, 'franchise_id' => null, 'owner_type' => 'institute', 'balance' => 0]
            );
            $opBal = (float) $sw->balance;
            $clBal = $opBal + $amount;
            $sw->update(['balance' => $clBal]);

            StudentTransaction::create([
                'user_id'      => $user->id,
                'institute_id' => $iid,
                'franchise_id' => null,
                'owner_type'   => 'institute',
                'description'  => 'Manual Credit | ' . $data['note'],
                'credit'       => $amount,
                'debit'        => 0,
                'type'         => 5,
                'ref_type'     => 'manual_adjustment',
                'ref_id'       => null,
                'date'         => now()->toDateString(),
                'c_date'       => now(),
                'op_bal'       => $opBal,
                'cl_bal'       => $clBal,
                'by_user_id'   => $byUser,
            ]);

            $iw = InstituteStudentWallet::firstOrCreate(
                ['institute_id' => $iid],
                ['balance' => 0]
            );
            InstituteStudentTransaction::create([
                'institute_id' => $iid,
                'ref_user_id'  => $user->id,
                'description'  => 'Manual wallet credit to ' . ($user->profile?->name ?? $user->user_id) . ' | ' . $data['note'],
                'credit'       => 0,
                'debit'        => $amount,
                'type'         => 2,
                'date'         => now()->toDateString(),
                'c_date'       => now(),
                'op_bal'       => $iw->balance,
                'cl_bal'       => $iw->balance - $amount,
                'by_user_id'   => $byUser,
            ]);
            $iw->update(['balance' => $iw->balance - $amount]);
        });

        return back()->with('success', 'Credit of ₹' . number_format($data['amount'], 2) . ' applied to student wallet.');
    }

    public function debit(Request $request, User $user)
    {
        if ($user->institute_id !== $this->instituteId()) {
            abort(403);
        }

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:500000',
            'note'   => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($data, $user) {
            $iid    = $this->instituteId();
            $amount = (float) $data['amount'];
            $byUser = Auth::guard('institute')->id();

            $sw = StudentWallet::firstOrCreate(
                ['user_id' => $user->id],
                ['institute_id' => $iid, 'franchise_id' => null, 'owner_type' => 'institute', 'balance' => 0]
            );
            $opBal = (float) $sw->balance;
            $clBal = $opBal - $amount;
            $sw->update(['balance' => $clBal]);

            StudentTransaction::create([
                'user_id'      => $user->id,
                'institute_id' => $iid,
                'franchise_id' => null,
                'owner_type'   => 'institute',
                'description'  => 'Manual Debit | ' . $data['note'],
                'credit'       => 0,
                'debit'        => $amount,
                'type'         => 4,
                'ref_type'     => 'manual_adjustment',
                'ref_id'       => null,
                'date'         => now()->toDateString(),
                'c_date'       => now(),
                'op_bal'       => $opBal,
                'cl_bal'       => $clBal,
                'by_user_id'   => $byUser,
            ]);
        });

        return back()->with('success', 'Debit of ₹' . number_format($data['amount'], 2) . ' applied to student wallet.');
    }
}
