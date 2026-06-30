@extends('layouts.institute')
@section('title','Fee Collection Report')
@section('page-title','Fee Collection Report')

@push('styles')
<style>
.cr-tabs { display:flex; gap:0; border-bottom:2px solid var(--border); margin-bottom:22px; }
.cr-tab { padding:10px 26px; font-weight:700; font-size:13px; cursor:pointer;
          border-bottom:3px solid transparent; margin-bottom:-2px;
          color:var(--text-2); transition:.15s; }
.cr-tab.active { color:var(--accent); border-bottom-color:var(--accent); }
.cr-section { display:none; }
.cr-section.active { display:block; }

.cr-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:22px; }
@media(max-width:700px){ .cr-stats { grid-template-columns:1fr 1fr; } }
.cr-stat { background:var(--bg-2); border:1px solid var(--border); border-radius:12px; padding:14px 18px; }
.cr-stat-num { font-size:24px; font-weight:900; }
.cr-stat-label { font-size:11px; color:var(--text-2); margin-top:3px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; }

.cr-tbl { width:100%; border-collapse:collapse; font-size:13px; }
.cr-tbl thead th { background:var(--bg-3); padding:10px 14px; text-align:left;
  font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;
  color:var(--text-2); border-bottom:1px solid var(--border); }
.cr-tbl tbody td { padding:11px 14px; border-bottom:1px solid var(--border); }
.cr-tbl tbody tr:last-child td { border-bottom:none; }
.cr-tbl tbody tr:hover { background:var(--bg-3); }

.mode-chip { display:inline-block; font-size:11px; font-weight:700; padding:2px 9px;
  border-radius:6px; text-transform:uppercase; letter-spacing:.04em; }
.m-cash   { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; }
.m-upi    { background:#f5f3ff; color:#5b4ec7; border:1px solid #ddd6fe; }
.m-neft   { background:#f5f3ff; color:#5b4ec7; border:1px solid #ddd6fe; }
.m-imps   { background:#f5f3ff; color:#5b4ec7; border:1px solid #ddd6fe; }
.m-cheque { background:#fef9c3; color:#a16207; border:1px solid #fde68a; }

.bar-track { height:8px; background:var(--bg-3); border-radius:4px; overflow:hidden; margin-top:5px; }
.bar-fill { height:100%; background:var(--accent); border-radius:4px; }
</style>
@endpush

@section('content')

{{-- Tabs --}}
<div class="cr-tabs">
  <div class="cr-tab active" onclick="switchTab('daily',this)">Today's Collection</div>
  <div class="cr-tab" onclick="switchTab('monthly',this)">Monthly Report</div>
</div>

{{-- ── Daily Tab ── --}}
<div class="cr-section active" id="tab-daily">

  <div class="cr-stats">
    <div class="cr-stat">
      <div class="cr-stat-num" style="color:var(--accent)">₹{{ number_format($dailyTotal, 0) }}</div>
      <div class="cr-stat-label">Total Today</div>
    </div>
    <div class="cr-stat">
      <div class="cr-stat-num" style="color:#16a34a">₹{{ number_format($dailyByCash, 0) }}</div>
      <div class="cr-stat-label">Cash</div>
    </div>
    <div class="cr-stat">
      <div class="cr-stat-num" style="color:#5b4ec7">₹{{ number_format($dailyByUpi, 0) }}</div>
      <div class="cr-stat-label">UPI / NEFT / IMPS</div>
    </div>
    <div class="cr-stat">
      <div class="cr-stat-num" style="color:#a16207">₹{{ number_format($dailyByCheque, 0) }}</div>
      <div class="cr-stat-label">Cheque</div>
    </div>
  </div>

  <div class="gt-card" style="padding:0;">
    <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
      <div class="gt-card-title" style="margin:0;">
        Transactions — {{ \Carbon\Carbon::parse($today)->format('d F Y') }}
      </div>
      <span style="font-size:12px;color:var(--text-2);">{{ $dailyRows->count() }} payment{{ $dailyRows->count() !== 1 ? 's' : '' }}</span>
    </div>

    @if($dailyRows->isEmpty())
      <div style="padding:48px;text-align:center;color:var(--text-2);">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.35;margin-bottom:10px;"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <div style="font-size:13px;font-weight:600;">No collections today</div>
      </div>
    @else
      <div style="overflow-x:auto;">
        <table class="cr-tbl">
          <thead>
            <tr>
              <th>Student</th>
              <th>Course</th>
              <th>Mode</th>
              <th>Invoice</th>
              <th style="text-align:right;">Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($dailyRows as $row)
              <tr>
                <td>
                  <div style="font-weight:600;">{{ $row->courseBook?->student?->profile?->name ?? '—' }}</div>
                  <div style="font-size:11px;color:var(--text-2);">{{ $row->courseBook?->student?->mobile ?? '' }}</div>
                </td>
                <td style="color:var(--text-2);">{{ $row->courseBook?->course?->name ?? '—' }}</td>
                <td><span class="mode-chip m-{{ strtolower($row->payment_mode) }}">{{ $row->payment_mode }}</span></td>
                <td style="font-size:12px;color:var(--text-2);">{{ $row->invoice_no ?? '—' }}</td>
                <td style="text-align:right;font-weight:800;font-size:14px;">₹{{ number_format($row->amount_value, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

</div>

{{-- ── Monthly Tab ── --}}
<div class="cr-section" id="tab-monthly">

  {{-- Month picker --}}
  <form method="GET" action="{{ route('institute.fees.collection-report') }}" style="margin-bottom:18px;display:flex;align-items:center;gap:10px;" data-no-spinner>
    <label style="font-size:13px;font-weight:600;color:var(--text-2);">Month:</label>
    <select name="month" onchange="this.form.submit()" class="gt-input" style="width:200px;padding:7px 12px;">
      @foreach($availableMonths as $ym)
        <option value="{{ $ym }}" {{ $ym === $month ? 'selected' : '' }}>
          {{ \Carbon\Carbon::createFromFormat('Y-m', $ym)->format('F Y') }}
        </option>
      @endforeach
    </select>
  </form>

  <div class="cr-stats">
    <div class="cr-stat">
      <div class="cr-stat-num" style="color:var(--accent)">₹{{ number_format($monthlyTotal, 0) }}</div>
      <div class="cr-stat-label">Total This Month</div>
    </div>
    <div class="cr-stat">
      <div class="cr-stat-num">{{ $monthlyRows->count() }}</div>
      <div class="cr-stat-label">Transactions</div>
    </div>
    @foreach($monthlyByMode as $mode => $amt)
      <div class="cr-stat">
        <div class="cr-stat-num">₹{{ number_format($amt, 0) }}</div>
        <div class="cr-stat-label">{{ $mode }}</div>
      </div>
    @endforeach
  </div>

  {{-- Day-wise breakdown --}}
  @if($monthlyByDate->isNotEmpty())
  <div class="gt-card" style="padding:18px 20px;margin-bottom:18px;">
    <div class="gt-card-title" style="margin-bottom:14px;">Day-wise Collection</div>
    @php $maxDay = $monthlyByDate->max(); @endphp
    @foreach($monthlyByDate as $date => $amt)
      <div style="display:grid;grid-template-columns:100px 1fr 100px;gap:10px;align-items:center;margin-bottom:8px;">
        <div style="font-size:12px;color:var(--text-2);">{{ \Carbon\Carbon::parse($date)->format('d M') }}</div>
        <div class="bar-track"><div class="bar-fill" style="width:{{ $maxDay > 0 ? round($amt/$maxDay*100) : 0 }}%"></div></div>
        <div style="font-size:13px;font-weight:700;text-align:right;">₹{{ number_format($amt, 0) }}</div>
      </div>
    @endforeach
  </div>
  @endif

  {{-- Transaction list --}}
  <div class="gt-card" style="padding:0;">
    <div style="padding:14px 18px;border-bottom:1px solid var(--border);">
      <div class="gt-card-title" style="margin:0;">All Transactions — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</div>
    </div>
    @if($monthlyRows->isEmpty())
      <div style="padding:40px;text-align:center;color:var(--text-2);font-size:13px;">No transactions for this month.</div>
    @else
      <div style="overflow-x:auto;">
        <table class="cr-tbl">
          <thead>
            <tr>
              <th>Date</th>
              <th>Student</th>
              <th>Course</th>
              <th>Mode</th>
              <th style="text-align:right;">Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($monthlyRows as $row)
              <tr>
                <td style="font-size:12px;color:var(--text-2);white-space:nowrap;">{{ \Carbon\Carbon::parse($row->date)->format('d M') }}</td>
                <td>
                  <div style="font-weight:600;">{{ $row->courseBook?->student?->profile?->name ?? '—' }}</div>
                  <div style="font-size:11px;color:var(--text-2);">{{ $row->courseBook?->student?->mobile ?? '' }}</div>
                </td>
                <td style="color:var(--text-2);">{{ $row->courseBook?->course?->name ?? '—' }}</td>
                <td><span class="mode-chip m-{{ strtolower($row->payment_mode) }}">{{ $row->payment_mode }}</span></td>
                <td style="text-align:right;font-weight:800;font-size:14px;">₹{{ number_format($row->amount_value, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr style="background:var(--bg-3);">
              <td colspan="4" style="padding:11px 14px;font-weight:800;font-size:13px;">Total</td>
              <td style="text-align:right;font-weight:900;font-size:15px;padding:11px 14px;">₹{{ number_format($monthlyTotal, 2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    @endif
  </div>

</div>

@endsection

@push('scripts')
<script>
function switchTab(id, el) {
  document.querySelectorAll('.cr-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.cr-section').forEach(s => s.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('tab-' + id).classList.add('active');
}

// Restore monthly tab if month param present
@if(request()->has('month'))
  document.querySelectorAll('.cr-tab')[1].click();
@endif
</script>
@endpush
