<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner\{Feature, Institute, InstitutePayCollect, InstituteWallet, Plan};
use App\Services\{InstituteOnboardingService, InvoiceService, WalletService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstituteController extends Controller
{
    public function __construct(
        protected InstituteOnboardingService $onboarding,
        protected WalletService              $walletService,
        protected InvoiceService             $invoiceService,
    ) {}

    public function index()
    {
        $institutes = Institute::with(['subscription.plan', 'wallet'])->latest()->get();
        return view('owner.institutes.index', compact('institutes'));
    }

    public function create()
    {
        $plans    = Plan::where('status', 'active')->with('features')->get();
        $features = Feature::where('status', 'active')->get();
        return view('owner.institutes.create', compact('plans', 'features'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:150',
            'short_name'         => 'nullable|string|max:50',
            'email'              => 'required|email|unique:institutes,email|unique:users,email',
            'mobile'             => 'required|string|max:15',
            'owner_name'         => 'required|string|max:100',
            'owner_mobile'       => 'required|string|max:15',
            'address'            => 'nullable|string',
            'state'              => 'nullable|string|max:60',
            'pin_code'           => 'nullable|string|max:10',
            'website'            => 'nullable|url|max:150',
            'type'               => 'required|in:PRIVATE,GOVT,FRANCHISE',
            'logo'               => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'plan_id'            => 'required|exists:plans,id',
            'addon_feature_ids'  => 'nullable|array',
            'addon_feature_ids.*'=> 'exists:features,id',
            'discount_type'      => 'required|in:NONE,PERCENT,FLAT',
            'discount_value'     => 'nullable|numeric|min:0',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo'] = 'storage/' . $logoPath;
        }

        try {
            $institute = $this->onboarding->create($data);
            return redirect()
                ->route('owner.institutes.show', $institute)
                ->with('success', "Institute '{$institute->name}' created! Login credentials sent to {$institute->email}.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Institute $institute)
    {
        $institute->load(['subscription.plan', 'features.feature', 'wallet', 'payCollects']);
        return view('owner.institutes.show', compact('institute'));
    }

    public function edit(Institute $institute)
    {
        return view('owner.institutes.edit', compact('institute'));
    }

    public function update(Request $request, Institute $institute)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:150',
            'short_name'   => 'nullable|string|max:50',
            'mobile'       => 'required|string|max:15',
            'owner_name'   => 'required|string|max:100',
            'owner_mobile' => 'required|string|max:15',
            'address'      => 'nullable|string',
            'state'        => 'nullable|string|max:60',
            'pin_code'     => 'nullable|string|max:10',
            'website'      => 'nullable|url|max:150',
            'type'         => 'required|in:PRIVATE,GOVT,FRANCHISE',
        ]);
        $institute->update($data);
        return redirect()->route('owner.institutes.show', $institute)->with('success', 'Institute updated.');
    }

    public function destroy(Institute $institute)
    {
        $institute->delete();
        return redirect()->route('owner.institutes.index')->with('success', 'Institute deleted.');
    }

    public function toggle(Institute $institute)
    {
        $new = $institute->status === 'active' ? 'inactive' : 'active';
        $institute->update(['status' => $new]);
        return back()->with('success', "Institute status changed to {$new}.");
    }

    public function transactions(Institute $institute)
    {
        $transactions = $institute->transactions()->paginate(20);
        $wallet       = $institute->wallet;
        return view('owner.institutes.transactions', compact('institute', 'transactions', 'wallet'));
    }

    public function recordPayment(Request $request, Institute $institute)
    {
        $data = $request->validate([
            'payment_mode' => 'required|in:CASH,UPI,NEFT,IMPS,CHEQUE',
            'utr'          => 'nullable|string|max:80',
            'amt'          => 'required|numeric|min:1',
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:255',
        ]);

        $invoiceNo = $this->invoiceService->generateInstituteInvoice();

        // Record payment collection
        InstitutePayCollect::create([
            'institute_id' => $institute->id,
            'invoice_no'   => $invoiceNo,
            'payment_mode' => $data['payment_mode'],
            'utr'          => $data['utr'] ?? null,
            'amt'          => $data['amt'],
            'date'         => $data['date'],
            'note'         => $data['note'] ?? null,
            'status'       => 'received',
            'received_by'  => Auth::guard('web')->id(),
            'c_date'       => now(),
        ]);

        // Credit to wallet
        $this->walletService->credit(
            $institute->id,
            (float)$data['amt'],
            "{$data['payment_mode']} Payment Received | Invoice: {$invoiceNo}" . ($data['note'] ? " | {$data['note']}" : ''),
            3,
            $invoiceNo
        );

        return back()->with('success', "Payment of ₹{$data['amt']} recorded. Invoice: {$invoiceNo}");
    }

    public function resendCredentials(Institute $institute)
    {
        // Re-send credentials: generate new password, update user
        $plain = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ0123456789'), 0, 10));
        $user  = $institute->head;
        if ($user) {
            $user->update(['password' => bcrypt($plain)]);
            try {
                \Mail::to($institute->email)->send(
                    new \App\Mail\InstituteWelcomeMail($institute, $user, $plain)
                );
                return back()->with('success', "Credentials resent to {$institute->email}.");
            } catch (\Throwable $e) {
                return back()->with('error', 'Mail failed: ' . $e->getMessage());
            }
        }
        return back()->with('error', 'No institute head user found.');
    }
}
