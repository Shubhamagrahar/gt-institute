@extends('layouts.owner')
@section('title', $institute->name)
@section('page-title', $institute->name)
@section('topbar-actions')
  <a href="{{ route('owner.institutes.edit',$institute) }}" class="btn btn-outline btn-sm">Edit</a>
  <a href="{{ route('owner.institutes.transactions',$institute) }}" class="btn btn-outline btn-sm">Ledger</a>
  <form action="{{ route('owner.institutes.resend-credentials',$institute) }}" method="POST" style="display:inline;">
    @csrf
    <button class="btn btn-outline btn-sm">Resend Credentials</button>
  </form>
@endsection

@section('content')
<div class="gt-grid-2" style="gap:20px;align-items:start;">

  <div>
    {{-- Info Card --}}
    <div class="gt-card mb-3">
      <div class="gt-card-header">
        <div class="gt-card-title">Institute Info</div>
        <span class="badge {{ $institute->status==='active' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($institute->status) }}</span>
      </div>
      <table class="gt-table">
        <tr><td class="text-muted" style="width:140px;">Unique ID</td><td><code style="color:var(--accent);">{{ $institute->unique_id }}</code></td></tr>
        <tr><td class="text-muted">Name</td><td class="fw-600">{{ $institute->name }}</td></tr>
        <tr><td class="text-muted">Short Name</td><td>{{ $institute->short_name ?? '—' }}</td></tr>
        <tr><td class="text-muted">Email</td><td>{{ $institute->email }}</td></tr>
        <tr><td class="text-muted">Mobile</td><td>{{ $institute->mobile }}</td></tr>
        <tr><td class="text-muted">Owner</td><td>{{ $institute->owner_name }} · {{ $institute->owner_mobile }}</td></tr>
        <tr><td class="text-muted">Type</td><td>{{ $institute->type }}</td></tr>
        <tr><td class="text-muted">State</td><td>{{ $institute->state ?? '—' }}</td></tr>
        <tr><td class="text-muted">PIN</td><td>{{ $institute->pin_code ?? '—' }}</td></tr>
        <tr><td class="text-muted">Website</td><td>{{ $institute->website ?? '—' }}</td></tr>
        <tr><td class="text-muted">Created</td><td>{{ $institute->created_at->format('d M Y') }}</td></tr>
      </table>
    </div>

    {{-- Features --}}
    <div class="gt-card">
      <div class="gt-card-header"><div class="gt-card-title">Active Features</div></div>
      <div style="display:flex;flex-wrap:wrap;gap:8px;">
        @forelse($institute->features as $if)
        <span class="badge {{ $if->is_addon ? 'badge-warning' : 'badge-success' }}">
          {{ $if->feature->name }}
          @if($if->is_addon) <span style="opacity:.7;">(+₹{{ number_format($if->price,0) }})</span>@endif
        </span>
        @empty
        <span class="text-muted text-sm">No features assigned</span>
        @endforelse
      </div>
    </div>
  </div>

  <div>
    {{-- Subscription --}}
    <div class="gt-card mb-3">
      <div class="gt-card-header"><div class="gt-card-title">Current Subscription</div></div>
      @if($institute->subscription)
      @php $sub = $institute->subscription; @endphp
      <table class="gt-table">
        <tr><td class="text-muted" style="width:140px;">Plan</td><td class="fw-600 text-accent">{{ $sub->plan->name }}</td></tr>
        <tr><td class="text-muted">Start</td><td>{{ \Carbon\Carbon::parse($sub->start_date)->format('d M Y') }}</td></tr>
        <tr><td class="text-muted">End</td><td>{{ \Carbon\Carbon::parse($sub->end_date)->format('d M Y') }}</td></tr>
        <tr><td class="text-muted">Plan Price</td><td class="mono">₹{{ number_format($sub->price,2) }}</td></tr>
        @if($sub->discount_amount > 0)
        <tr><td class="text-muted">Discount</td><td class="mono amount-neg">−₹{{ number_format($sub->discount_amount,2) }} ({{ $sub->discount_type === 'PERCENT' ? $sub->discount_value.'%' : 'Flat' }})</td></tr>
        @endif
        <tr><td class="text-muted">Final Price</td><td class="mono fw-700 text-accent">₹{{ number_format($sub->final_price,2) }}</td></tr>
        <tr><td class="text-muted">Status</td><td><span class="badge badge-success">{{ ucfirst($sub->status) }}</span></td></tr>
      </table>
      @else
      <div class="text-muted text-sm">No active subscription</div>
      @endif
    </div>

    {{-- Wallet + Record Payment --}}
    <div class="gt-card mb-3">
      <div class="gt-card-header">
        <div class="gt-card-title">Wallet Balance</div>
        <span class="mono fw-700 {{ ($institute->wallet?->main_b ?? 0) >= 0 ? 'amount-pos' : 'amount-neg' }}" style="font-size:18px;">
          ₹{{ number_format($institute->wallet?->main_b ?? 0, 2) }}
        </span>
      </div>

      <div style="border-top:1px solid var(--border);padding-top:16px;">
        <div class="text-sm fw-600 mb-2" style="margin-bottom:12px;">Record Payment Received</div>
        <form method="POST" action="{{ route('owner.institutes.payment',$institute) }}">
          @csrf
          <div class="gt-form-grid-2">
            <div class="gt-form-group">
              <label class="gt-label">Mode</label>
              <select name="payment_mode" class="gt-select">
                @foreach(['CASH','UPI','NEFT','IMPS','CHEQUE'] as $m)
                <option value="{{ $m }}">{{ $m }}</option>
                @endforeach
              </select>
            </div>
            <div class="gt-form-group">
              <label class="gt-label">Amount (₹)</label>
              <input type="number" name="amt" class="gt-input" min="1" step="0.01" required placeholder="0.00">
            </div>
          </div>
          <div class="gt-form-grid-2">
            <div class="gt-form-group">
              <label class="gt-label">Date</label>
              <input type="date" name="date" class="gt-input" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="gt-form-group">
              <label class="gt-label">UTR / Ref No.</label>
              <input type="text" name="utr" class="gt-input" placeholder="Optional">
            </div>
          </div>
          <div class="gt-form-group">
            <label class="gt-label">Note</label>
            <input type="text" name="note" class="gt-input" placeholder="Optional note">
          </div>
          <button type="submit" class="btn btn-success w-full" style="justify-content:center;">Record Payment</button>
        </form>
      </div>
    </div>

    {{-- Payment History --}}
    <div class="gt-card">
      <div class="gt-card-header">
        <div class="gt-card-title">Payment History</div>
        <a href="{{ route('owner.institutes.transactions',$institute) }}" class="btn btn-outline btn-xs">Full Ledger</a>
      </div>
      @forelse($institute->payCollects->take(5) as $pay)
      <div class="flex justify-between items-center" style="padding:8px 0;border-bottom:1px solid var(--border);">
        <div>
          <div class="text-sm fw-600">{{ $pay->payment_mode }} · <code style="font-size:11px;color:var(--accent);">{{ $pay->invoice_no }}</code></div>
          <div class="text-xs text-muted">{{ \Carbon\Carbon::parse($pay->date)->format('d M Y') }}</div>
        </div>
        <span class="mono amount-pos">+₹{{ number_format($pay->amt,2) }}</span>
      </div>
      @empty
      <div class="text-muted text-sm">No payments recorded</div>
      @endforelse
    </div>
  </div>

</div>
@endsection
