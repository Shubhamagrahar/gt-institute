@extends('layouts.institute')
@section('title','Student Fee')
@section('page-title','Fee Collection')

@section('content')
<div class="gt-grid-2" style="gap:20px;align-items:start;">

  {{-- Left: Student info + collect fee --}}
  <div style="display:flex;flex-direction:column;gap:16px;">
    {{-- Student card --}}
    <div class="gt-card">
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:14px;">
        @if($user->profile?->photo)
          <img src="{{ asset($user->profile->photo) }}"
            style="width:54px;height:54px;object-fit:cover;border-radius:50%;">
        @else
          <div style="width:54px;height:54px;border-radius:50%;background:var(--accent-bg);
            display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:var(--accent);">
            {{ strtoupper(substr($user->profile?->name ?? $user->user_id, 0, 1)) }}
          </div>
        @endif
        <div>
          <div style="font-size:16px;font-weight:700;">{{ $user->profile?->name ?? $user->user_id }}</div>
          <div class="text-xs text-muted mono">{{ $user->user_id }}</div>
          <div class="text-xs text-muted">{{ $user->mobile }}</div>
        </div>
      </div>
      <div style="display:flex;justify-content:space-between;padding:10px 14px;
        background:{{ $wallet?->balance < 0 ? 'var(--danger-bg)' : 'var(--success-bg)' }};
        border-radius:8px;">
        <span style="font-size:13px;">{{ $wallet?->balance < 0 ? 'Due Amount' : 'Advance Balance' }}</span>
        <span class="mono fw-700 {{ $wallet?->balance < 0 ? 'amount-neg' : 'amount-pos' }}" style="font-size:18px;">
          ₹{{ number_format(abs($wallet?->balance ?? 0), 2) }}
        </span>
      </div>
    </div>

    {{-- Collect fee form --}}
    @if($wallet?->balance < 0)
    <div class="gt-card">
      <div class="gt-card-title" style="margin-bottom:16px;">Collect Fee</div>
      <form method="POST" action="{{ route('institute.fee-collect.collect', $user) }}">
        @csrf
        <div class="gt-form-group">
          <label class="gt-label">Amount (₹) <span style="color:var(--danger)">*</span></label>
          <input type="number" name="amount" class="gt-input" min="1"
            max="{{ abs($wallet->balance) }}"
            placeholder="Enter amount" required>
        </div>
        <div class="gt-form-grid-2">
          <div class="gt-form-group">
            <label class="gt-label">Payment Mode <span style="color:var(--danger)">*</span></label>
            <select name="payment_mode" class="gt-select" required>
              <option value="">Select</option>
              <option>CASH</option><option>UPI</option>
              <option>NEFT</option><option>IMPS</option><option>CHEQUE</option>
            </select>
          </div>
          <div class="gt-form-group">
            <label class="gt-label">Date <span style="color:var(--danger)">*</span></label>
            <input type="date" name="date" class="gt-input" value="{{ date('Y-m-d') }}" required>
          </div>
        </div>
        <div class="gt-form-group">
          <label class="gt-label">UTR / Reference No</label>
          <input type="text" name="utr" class="gt-input" placeholder="Transaction reference">
        </div>
        <div class="gt-form-group">
          <label class="gt-label">Note</label>
          <input type="text" name="note" class="gt-input" placeholder="Optional note">
        </div>
        <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">
          Collect Fee
        </button>
      </form>
    </div>
    @endif

    {{-- Enrollments --}}
    <div class="gt-card">
      <div class="gt-card-title" style="margin-bottom:12px;">Enrollments</div>
      @foreach($enrollments as $e)
      <div style="padding:10px 12px;background:var(--bg-3);border-radius:8px;margin-bottom:8px;">
        <div style="display:flex;justify-content:space-between;">
          <span class="fw-600 text-sm">{{ $e->course->name }}</span>
          <span class="badge {{ $e->status === 'RUN' ? 'badge-success' : 'badge-neutral' }}">{{ $e->status }}</span>
        </div>
        <div class="text-xs text-muted mono">{{ $e->enrollment_no }}</div>
        <div style="margin-top:6px;font-size:12px;">
          Total: <span class="mono fw-600">₹{{ number_format($e->final_fee,2) }}</span>
          @if($e->paymentPlan)
            · <span class="badge badge-accent" style="font-size:10px;">{{ $e->paymentPlan->plan_type }}</span>
          @endif
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Right: Transaction history --}}
  <div style="display:flex;flex-direction:column;gap:16px;">
    {{-- Fee receipts --}}
    <div class="gt-card">
      <div class="gt-card-title" style="margin-bottom:12px;">Fee Receipts</div>
      @forelse($receipts as $r)
      <div style="display:flex;justify-content:space-between;align-items:center;
        padding:9px 0;border-bottom:1px solid var(--border);">
        <div>
          <div style="font-size:13px;font-weight:600;">₹{{ number_format($r->amount,2) }}</div>
          <div class="text-xs text-muted">{{ $r->payment_mode }} · {{ $r->date->format('d M Y') }}</div>
          <div class="text-xs mono text-muted">{{ $r->invoice_no }}</div>
        </div>
        <a href="{{ route('institute.fee-collect.receipt', [$user, $r]) }}"
          class="btn btn-outline btn-xs">Receipt</a>
      </div>
      @empty
        <div class="text-xs text-muted">No payments collected yet.</div>
      @endforelse
    </div>

    {{-- Transaction ledger --}}
    <div class="gt-card">
      <div class="gt-card-title" style="margin-bottom:12px;">Transaction Ledger</div>
      <div class="gt-table-wrap">
        <table class="gt-table">
          <thead>
            <tr><th>Date</th><th>Description</th><th>Dr</th><th>Cr</th><th>Balance</th></tr>
          </thead>
          <tbody>
            @forelse($transactions as $t)
            <tr>
              <td class="text-xs">{{ \Carbon\Carbon::parse($t->date)->format('d M Y') }}</td>
              <td style="font-size:12px;">{{ $t->description }}</td>
              <td class="mono text-xs amount-neg">{{ $t->debit > 0 ? '₹'.number_format($t->debit,2) : '' }}</td>
              <td class="mono text-xs amount-pos">{{ $t->credit > 0 ? '₹'.number_format($t->credit,2) : '' }}</td>
              <td class="mono text-xs {{ $t->cl_bal < 0 ? 'amount-neg' : 'amount-pos' }}">
                ₹{{ number_format(abs($t->cl_bal),2) }}
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-xs text-muted" style="text-align:center;">No transactions</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection