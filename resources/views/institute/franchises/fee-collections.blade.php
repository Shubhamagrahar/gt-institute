@extends('layouts.institute')
@section('title', 'Franchise Fee — ' . $franchise->name)
@section('page-title', 'Franchise Onboarding Fee')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.show', $franchise) }}" class="btn btn-outline btn-sm">← Back to Franchise</a>
@endsection

@section('content')

{{-- Summary cards --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:20px;">
  <div class="gt-card" style="padding:16px 20px;">
    <div class="text-xs text-muted" style="margin-bottom:6px;">Total Due</div>
    <div style="font-size:22px; font-weight:700; color:var(--accent);">₹{{ number_format($franchise->fee_total, 2) }}</div>
    <div class="text-xs text-muted" style="margin-top:4px;">{{ $franchise->level?->name ?? 'Standard Level' }}</div>
  </div>
  <div class="gt-card" style="padding:16px 20px;">
    <div class="text-xs text-muted" style="margin-bottom:6px;">Total Collected</div>
    <div style="font-size:22px; font-weight:700; color:#2a7a2a;">₹{{ number_format($totalPaid, 2) }}</div>
    <div class="text-xs text-muted" style="margin-top:4px;">{{ $collections->whereNull('cancelled_at')->count() }} payment(s)</div>
  </div>
  <div class="gt-card" style="padding:16px 20px;">
    <div class="text-xs text-muted" style="margin-bottom:6px;">Outstanding</div>
    <div style="font-size:22px; font-weight:700; color:{{ $outstanding > 0 ? 'var(--danger)' : '#2a7a2a' }};">₹{{ number_format($outstanding, 2) }}</div>
    <div class="text-xs text-muted" style="margin-top:4px;">{{ $outstanding > 0 ? 'Due from franchise' : 'Fully paid' }}</div>
  </div>
</div>

<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:flex-start;">

  {{-- Collections Table --}}
  <div class="gt-card">
    <div class="gt-card-header">
      <div class="gt-card-title">Payment History</div>
    </div>

    <div class="gt-table-wrap">
      <table class="gt-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Invoice No.</th>
            <th>Mode</th>
            <th>UTR / Ref</th>
            <th>Amount</th>
            <th>Note</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($collections as $col)
            <tr @if($col->cancelled_at) style="opacity:.5;" @endif>
              <td>{{ \Carbon\Carbon::parse($col->date)->format('d M Y') }}</td>
              <td><span class="mono" style="font-size:11px;">{{ $col->invoice_no ?: '—' }}</span></td>
              <td>{{ $col->payment_mode ?: '—' }}</td>
              <td class="text-muted" style="font-size:12px;">{{ $col->utr ?: '—' }}</td>
              <td>
                <span class="mono" style="color:{{ $col->cancelled_at ? 'var(--text-3)' : '#2a7a2a' }};">
                  ₹{{ number_format($col->amount, 2) }}
                </span>
              </td>
              <td class="text-muted" style="font-size:12px;">{{ $col->note ?: '—' }}</td>
              <td>
                @if($col->cancelled_at)
                  <span class="badge badge-warning">Cancelled</span>
                @else
                  <span class="badge badge-success">Received</span>
                @endif
              </td>
              <td>
                <div class="flex gap-2">
                  @unless($col->cancelled_at)
                    <a href="{{ route('institute.franchises.fee.receipt', [$franchise, $col]) }}" target="_blank"
                       class="btn btn-outline btn-xs">Receipt</a>
                    <button type="button" class="btn btn-xs"
                      style="background:var(--danger-bg,rgba(220,53,69,.12));color:var(--danger);border:1px solid rgba(220,53,69,.2);"
                      onclick="openCancelModal({{ $col->id }})">Cancel</button>
                  @endunless
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8">
                <div class="gt-empty">
                  <div class="gt-empty-title">No payments collected yet</div>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Collect Payment Panel --}}
  @if($outstanding > 0)
  <div class="gt-card" style="position:sticky; top:80px;">
    <div class="gt-card-header">
      <div class="gt-card-title">Collect Payment</div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" style="margin-bottom:14px; padding:10px 14px; background:rgba(42,122,42,.1); border:1px solid rgba(42,122,42,.3); border-radius:6px; color:#2a7a2a; font-size:13px;">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('institute.franchises.fee.collect', $franchise) }}">
      @csrf
      <div class="gt-form-group">
        <label class="gt-label">Payment Mode <span style="color:var(--danger)">*</span></label>
        <select name="payment_mode" class="gt-select" required>
          <option value="CASH">Cash</option>
          <option value="UPI">UPI</option>
          <option value="NEFT">NEFT</option>
          <option value="IMPS">IMPS</option>
          <option value="CHEQUE">Cheque</option>
        </select>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Amount (₹) <span style="color:var(--danger)">*</span></label>
        <input type="number" name="amount" class="gt-input" step="0.01" min="0.01" max="{{ $outstanding }}"
          value="{{ $outstanding }}" required>
        <div class="text-xs text-muted" style="margin-top:4px;">Outstanding: ₹{{ number_format($outstanding, 2) }}</div>
        @error('amount')<div class="gt-error">{{ $message }}</div>@enderror
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Date <span style="color:var(--danger)">*</span></label>
        <input type="date" name="date" class="gt-input" value="{{ now()->toDateString() }}" required>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">UTR / Ref No.</label>
        <input type="text" name="utr" class="gt-input" placeholder="Optional">
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Note</label>
        <input type="text" name="note" class="gt-input" placeholder="Optional">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
        Collect ₹{{ number_format($outstanding, 2) }}
      </button>
    </form>
  </div>
  @else
  <div class="gt-card" style="padding:20px; text-align:center;">
    <div style="font-size:32px; margin-bottom:10px;">✅</div>
    <div class="fw-600" style="color:#2a7a2a; margin-bottom:6px;">Fully Paid</div>
    <div class="text-xs text-muted">All onboarding fees have been collected from this franchise.</div>
  </div>
  @endif

</div>

{{-- Cancel Modal --}}
<div id="cancel-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:999; display:none; align-items:center; justify-content:center;">
  <div style="background:var(--bg-2); border-radius:var(--radius); padding:24px; width:400px; max-width:95vw;">
    <div class="gt-card-title" style="margin-bottom:16px;">Cancel Payment</div>
    <form id="cancel-form" method="POST" action="">
      @csrf
      @method('PATCH')
      <div class="gt-form-group">
        <label class="gt-label">Reason for Cancellation <span style="color:var(--danger)">*</span></label>
        <input type="text" name="cancel_reason" class="gt-input" required placeholder="Enter reason">
      </div>
      <div class="flex gap-2" style="margin-top:14px;">
        <button type="submit" class="btn btn-danger" style="flex:1;">Cancel Payment</button>
        <button type="button" onclick="closeCancelModal()" class="btn btn-outline" style="flex:1;">Close</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function openCancelModal(id) {
  document.getElementById('cancel-form').action = '{{ url("institute/franchises/{$franchise->id}/fee") }}/' + id + '/cancel';
  const modal = document.getElementById('cancel-modal');
  modal.style.display = 'flex';
}
function closeCancelModal() {
  document.getElementById('cancel-modal').style.display = 'none';
}
</script>
@endpush

@endsection
