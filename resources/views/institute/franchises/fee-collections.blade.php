@extends('layouts.institute')
@section('title', 'Joining Fee — ' . $franchise->name)
@section('page-title', 'Franchise Joining Fee')
@section('topbar-actions')
  <a href="{{ route('institute.franchises.show', $franchise) }}" class="btn btn-outline btn-sm">← Franchise Profile</a>
@endsection

@section('content')

{{-- Header bar --}}
<div class="fee-header-bar">
  <div>
    <div style="font-size:16px;font-weight:700;color:var(--text-1);">{{ $franchise->name }}</div>
    <div style="font-size:12px;color:var(--text-3);margin-top:2px;">
      {{ $franchise->level?->name ?? '—' }} &nbsp;·&nbsp;
      <span style="color:{{ $franchise->management_type==='wallet'?'rgba(30,120,200,.9)':'rgba(42,138,74,.9)' }};font-weight:600;">
        {{ $franchise->management_type==='wallet' ? 'Wallet System' : 'Independent' }}
      </span>
      @if($franchise->unique_id)
        &nbsp;·&nbsp; <span class="mono" style="font-size:11px;">{{ $franchise->unique_id }}</span>
      @endif
    </div>
  </div>
  @if($franchise->management_type === 'wallet')
    <a href="{{ route('institute.franchises.transactions', $franchise) }}" class="btn btn-outline btn-sm">
      Operational Wallet →
    </a>
  @endif
</div>

{{-- Wallet summary cards --}}
<div class="fee-summary-grid">
  <div class="fee-sum-card">
    <div class="fee-sum-label">Total Due</div>
    <div class="fee-sum-val" style="color:var(--accent);">₹{{ number_format($totalDue, 2) }}</div>
    <div class="fee-sum-sub">{{ $franchise->level?->name ?? 'Level fee' }}</div>
  </div>
  <div class="fee-sum-card">
    <div class="fee-sum-label">Collected</div>
    <div class="fee-sum-val" style="color:#16a34a;">₹{{ number_format($totalPaid, 2) }}</div>
    <div class="fee-sum-sub">{{ $collections->whereNull('cancelled_at')->count() }} payment(s)</div>
  </div>
  <div class="fee-sum-card {{ $outstanding > 0 ? 'fee-sum-danger' : 'fee-sum-success' }}">
    <div class="fee-sum-label">Outstanding</div>
    <div class="fee-sum-val" style="color:{{ $outstanding > 0 ? '#dc2626' : '#16a34a' }};">
      ₹{{ number_format($outstanding, 2) }}
    </div>
    <div class="fee-sum-sub">{{ $outstanding > 0 ? 'Pending from franchise' : '✓ Fully settled' }}</div>
  </div>
  <div class="fee-sum-card">
    <div class="fee-sum-label">Cancelled</div>
    <div class="fee-sum-val" style="color:var(--text-3);">₹{{ number_format($cancelled, 2) }}</div>
    <div class="fee-sum-sub">{{ $collections->whereNotNull('cancelled_at')->count() }} entry(s)</div>
  </div>
</div>

<div class="fee-layout">

  {{-- ── Ledger ──────────────────────────────────────────────── --}}
  <div class="gt-card">
    <div class="gt-card-header" style="border-bottom:1px solid var(--border-1);padding-bottom:12px;">
      <div class="gt-card-title">Joining Fee Ledger</div>
    </div>

    <div class="gt-table-wrap">
      <table class="gt-table fee-ledger-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Invoice</th>
            <th>Mode</th>
            <th>UTR / Ref</th>
            <th style="text-align:right;">Credit (₹)</th>
            <th style="text-align:right;">Balance (₹)</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          {{-- Opening row --}}
          <tr class="fee-opening-row">
            <td>—</td>
            <td colspan="3" style="font-style:italic;color:var(--text-3);font-size:12px;">Opening — Joining fee charged</td>
            <td style="text-align:right;" class="mono">—</td>
            <td style="text-align:right;font-weight:700;" class="mono">₹{{ number_format($totalDue, 2) }}</td>
            <td colspan="2"></td>
          </tr>

          @php
            $runningBalance = $totalDue;
          @endphp

          @forelse($collections as $col)
            @php
              if (!$col->cancelled_at) {
                $runningBalance -= (float) $col->amount;
              }
            @endphp
            <tr @if($col->cancelled_at) class="fee-cancelled-row" @endif>
              <td style="font-size:12.5px;">{{ \Carbon\Carbon::parse($col->date)->format('d M Y') }}</td>
              <td>
                @unless($col->cancelled_at)
                  <a href="{{ route('institute.franchises.fee.receipt', [$franchise, $col]) }}"
                     target="_blank" class="fee-invoice-link mono" style="font-size:11.5px;">
                    {{ $col->invoice_no ?: '—' }}
                  </a>
                @else
                  <span class="mono" style="font-size:11.5px;color:var(--text-3);">{{ $col->invoice_no ?: '—' }}</span>
                @endunless
              </td>
              <td>
                <span class="fee-mode-badge fee-mode-{{ strtolower($col->payment_mode ?? 'cash') }}">
                  {{ $col->payment_mode ?: '—' }}
                </span>
              </td>
              <td style="font-size:12px;color:var(--text-3);">{{ $col->utr ?: '—' }}</td>
              <td style="text-align:right;" class="mono">
                @unless($col->cancelled_at)
                  <span style="color:#16a34a;font-weight:600;">₹{{ number_format($col->amount, 2) }}</span>
                @else
                  <span style="color:var(--text-3);text-decoration:line-through;">₹{{ number_format($col->amount, 2) }}</span>
                @endunless
              </td>
              <td style="text-align:right;font-weight:600;" class="mono">
                @unless($col->cancelled_at)
                  <span style="color:{{ $runningBalance <= 0 ? '#16a34a' : 'var(--text-1)' }};">
                    ₹{{ number_format(max(0, $runningBalance), 2) }}
                  </span>
                @else
                  <span style="color:var(--text-3);">—</span>
                @endunless
              </td>
              <td>
                @if($col->cancelled_at)
                  <span class="badge badge-warning" style="font-size:10.5px;">Cancelled</span>
                @else
                  <span class="badge badge-success" style="font-size:10.5px;">Received</span>
                @endif
              </td>
              <td>
                @unless($col->cancelled_at)
                  <button type="button" class="btn btn-xs fee-cancel-btn"
                    onclick="openCancelModal({{ $col->id }})">Cancel</button>
                @endunless
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center;padding:28px;color:var(--text-3);font-size:13px;">
                No payments collected yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ── Collect Payment Panel ────────────────────────────────── --}}
  <div>
    @if(session('success'))
      <div class="fee-alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="fee-alert-error">{{ session('error') }}</div>
    @endif

    @if($outstanding > 0)
    <div class="gt-card fee-collect-card">
      <div class="gt-card-header" style="border-bottom:1px solid var(--border-1);padding-bottom:12px;">
        <div class="gt-card-title">Collect Payment</div>
      </div>

      <div class="fee-due-box">
        <div class="fee-due-label">Outstanding Amount</div>
        <div class="fee-due-amount">₹{{ number_format($outstanding, 2) }}</div>
      </div>

      <form method="POST" action="{{ route('institute.franchises.fee.collect', $franchise) }}" id="fee-collect-form">
        @csrf
        <div class="gt-form-group">
          <label class="gt-label">Payment Mode <span style="color:var(--danger)">*</span></label>
          <select name="payment_mode" id="fee-mode-sel" class="gt-select" required onchange="feeToggleUtr(this)">
            <option value="CASH">Cash</option>
            <option value="UPI">UPI</option>
            <option value="NEFT">NEFT</option>
            <option value="IMPS">IMPS</option>
            <option value="CHEQUE">Cheque</option>
          </select>
          @error('payment_mode')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

        <div class="gt-form-group">
          <label class="gt-label" id="fee-utr-label">UTR / Ref No.</label>
          <input type="text" name="utr" id="fee-utr-inp" class="gt-input"
                 value="{{ old('utr') }}" placeholder="Transaction / reference number">
          @error('utr')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

        <div class="gt-form-group">
          <label class="gt-label">Amount (₹) <span style="color:var(--danger)">*</span></label>
          <input type="number" name="amount" class="gt-input" step="0.01" min="0.01"
                 max="{{ $outstanding }}" value="{{ old('amount', $outstanding) }}" required>
          @error('amount')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

        <div class="gt-form-group">
          <label class="gt-label">Date <span style="color:var(--danger)">*</span></label>
          <input type="date" name="date" class="gt-input" value="{{ old('date', now()->toDateString()) }}" required>
          @error('date')<div class="gt-error">{{ $message }}</div>@enderror
        </div>

        <div class="gt-form-group">
          <label class="gt-label">Note</label>
          <input type="text" name="note" class="gt-input" value="{{ old('note') }}" placeholder="Optional">
        </div>

        <button type="submit" class="btn btn-primary" id="fee-submit-btn" style="width:100%;justify-content:center;">
          Collect ₹{{ number_format($outstanding, 2) }}
        </button>
      </form>
    </div>
    @else
    <div class="gt-card" style="padding:28px;text-align:center;">
      <div style="font-size:36px;margin-bottom:10px;">✅</div>
      <div style="font-size:15px;font-weight:700;color:#16a34a;margin-bottom:6px;">Fully Settled</div>
      <div style="font-size:12px;color:var(--text-3);">All joining fees have been collected from this franchise.</div>
    </div>
    @endif
  </div>

</div>

{{-- Cancel Modal --}}
<div id="cancel-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:var(--bg-2);border-radius:var(--radius);padding:24px;width:400px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,.3);">
    <div class="gt-card-title" style="margin-bottom:16px;">Cancel Payment Entry</div>
    <form id="cancel-form" method="POST" action="">
      @csrf @method('PATCH')
      <div class="gt-form-group">
        <label class="gt-label">Reason <span style="color:var(--danger)">*</span></label>
        <input type="text" name="cancel_reason" class="gt-input" required placeholder="Enter reason for cancellation">
      </div>
      <div style="display:flex;gap:8px;margin-top:14px;">
        <button type="submit" class="btn btn-danger" style="flex:1;">Confirm Cancel</button>
        <button type="button" onclick="closeCancelModal()" class="btn btn-outline" style="flex:1;">Close</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('styles')
<style>
.fee-header-bar{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px}
.fee-summary-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.fee-sum-card{background:var(--bg-2);border:1px solid var(--border-2);border-radius:var(--radius);padding:16px 20px}
.fee-sum-card.fee-sum-danger{border-color:rgba(220,38,38,.25);background:rgba(220,38,38,.04)}
.fee-sum-card.fee-sum-success{border-color:rgba(22,163,74,.25);background:rgba(22,163,74,.04)}
.fee-sum-label{font-size:11px;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
.fee-sum-val{font-size:22px;font-weight:800}
.fee-sum-sub{font-size:11.5px;color:var(--text-3);margin-top:4px}

.fee-layout{display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:flex-start}

.fee-ledger-table thead th{font-size:11px}
.fee-opening-row td{background:var(--bg-3);font-size:12.5px;color:var(--text-2)}
.fee-cancelled-row td{opacity:.5}
.fee-invoice-link{color:var(--accent);text-decoration:none}
.fee-invoice-link:hover{text-decoration:underline}

.fee-mode-badge{font-size:10.5px;font-weight:600;padding:2px 8px;border-radius:20px;display:inline-block}
.fee-mode-cash{background:rgba(22,163,74,.1);color:#16a34a;border:1px solid rgba(22,163,74,.2)}
.fee-mode-upi{background:rgba(138,115,245,.1);color:rgba(138,115,245,.9);border:1px solid rgba(138,115,245,.25)}
.fee-mode-neft,.fee-mode-imps{background:rgba(30,120,200,.1);color:#1e78c8;border:1px solid rgba(30,120,200,.2)}
.fee-mode-cheque{background:rgba(180,83,9,.1);color:#b45309;border:1px solid rgba(180,83,9,.2)}

.fee-cancel-btn{background:rgba(220,38,38,.08);color:#dc2626;border:1px solid rgba(220,38,38,.2);font-size:11px}
.fee-cancel-btn:hover{background:rgba(220,38,38,.18)}

.fee-collect-card{position:sticky;top:80px}
.fee-due-box{background:rgba(220,38,38,.06);border:1px solid rgba(220,38,38,.2);border-radius:var(--radius-sm);padding:12px 16px;margin-bottom:18px;text-align:center}
.fee-due-label{font-size:11px;color:#dc2626;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
.fee-due-amount{font-size:24px;font-weight:800;color:#dc2626}

.fee-alert-success{background:rgba(22,163,74,.1);border:1px solid rgba(22,163,74,.3);border-radius:var(--radius-sm);padding:10px 14px;color:#16a34a;font-size:13px;margin-bottom:14px}
.fee-alert-error{background:rgba(220,38,38,.1);border:1px solid rgba(220,38,38,.3);border-radius:var(--radius-sm);padding:10px 14px;color:#dc2626;font-size:13px;margin-bottom:14px}

@media(max-width:900px){
  .fee-summary-grid{grid-template-columns:1fr 1fr}
  .fee-layout{grid-template-columns:1fr}
}
</style>
@endpush

@push('scripts')
<script>
function feeToggleUtr(sel) {
  const needsUtr = ['UPI','NEFT','IMPS','CHEQUE'].includes(sel.value);
  const inp   = document.getElementById('fee-utr-inp');
  const label = document.getElementById('fee-utr-label');
  inp.required = needsUtr;
  label.innerHTML = needsUtr
    ? 'UTR / Ref No. <span style="color:#dc2626">*</span>'
    : 'UTR / Ref No.';
}

document.getElementById('fee-collect-form')?.addEventListener('submit', function () {
  const btn = document.getElementById('fee-submit-btn');
  btn.disabled = true;
  btn.textContent = 'Processing…';
});

function openCancelModal(id) {
  document.getElementById('cancel-form').action =
    '{{ url("institute/franchises/{$franchise->id}/fee") }}/' + id + '/cancel';
  document.getElementById('cancel-modal').style.display = 'flex';
}
function closeCancelModal() {
  document.getElementById('cancel-modal').style.display = 'none';
}
</script>
@endpush
