@extends('layouts.institute')
@section('title','Monthly Fees')
@section('page-title','Monthly Fee Collection')

@push('styles')
<style>
.mf-tabs{display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:20px}
.mf-tab{padding:10px 22px;font-weight:700;font-size:13px;cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;color:var(--text-2);transition:.15s;white-space:nowrap}
.mf-tab.active{color:var(--primary);border-bottom-color:var(--primary)}
.mf-tab.danger.active{color:#dc2626;border-bottom-color:#dc2626}
.mf-section{display:none}.mf-section.active{display:block}

.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px}
@media(max-width:768px){.stats-row{grid-template-columns:repeat(2,1fr)}}
.stat-box{background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:14px 18px}
.stat-num{font-size:28px;font-weight:900}
.stat-label{font-size:12px;color:var(--text-2);margin-top:2px}

.mf-card{display:grid;grid-template-columns:52px 1fr auto;gap:14px;align-items:center;padding:14px 18px;border-bottom:1px solid var(--border);transition:.15s}
.mf-card:last-child{border-bottom:none}
.mf-card:hover{background:var(--bg-3)}
.stu-avatar{width:44px;height:44px;border-radius:50%;object-fit:cover;background:var(--bg-3);border:2px solid var(--border)}
.stu-name{font-weight:700;font-size:14px}
.stu-sub{font-size:12px;color:var(--text-2);margin-top:2px}

.amount-chip{display:inline-block;padding:3px 10px;border-radius:8px;font-size:12px;font-weight:700;font-family:monospace}
.chip-due{background:#fffbeb;color:#b45309;border:1px solid #fcd34d}
.chip-overdue{background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5}
.chip-ok{background:#f0fdf4;color:#15803d;border:1px solid #86efac}

/* Pay modal */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center}
.modal-bg.open{display:flex}
.modal-box{background:var(--bg-1);border-radius:18px;padding:28px;width:100%;max-width:460px;box-shadow:0 24px 60px rgba(0,0,0,.25)}
.modal-title{font-size:18px;font-weight:800;margin-bottom:4px}
.modal-sub{font-size:13px;color:var(--text-2);margin-bottom:20px}
</style>
@endpush

@section('content')
@php
  $totalMonthly = $enrollments->count();
  $totalOverdue = $overdueList->count();
  $totalDue     = $dueList->count();
  $totalUpcoming= $upcomingList->count();
  $currentMonth = $today->format('F Y');
@endphp

{{-- Stats --}}
<div class="stats-row">
  <div class="stat-box">
    <div class="stat-num">{{ $totalMonthly }}</div>
    <div class="stat-label">Total Monthly Students</div>
  </div>
  <div class="stat-box">
    <div class="stat-num" style="color:#dc2626">{{ $totalOverdue }}</div>
    <div class="stat-label">Overdue (Late Fee Applicable)</div>
  </div>
  <div class="stat-box">
    <div class="stat-num" style="color:#d97706">{{ $totalDue }}</div>
    <div class="stat-label">Due This Month ({{ $currentMonth }})</div>
  </div>
  <div class="stat-box">
    <div class="stat-num" style="color:#16a34a">{{ $totalUpcoming }}</div>
    <div class="stat-label">Upcoming / Paid</div>
  </div>
</div>

{{-- Tabs --}}
<div class="mf-tabs">
  <div class="mf-tab danger {{ $totalOverdue > 0 ? 'active' : '' }}" onclick="switchTab('overdue',this)">
    Overdue
    @if($totalOverdue > 0)<span class="badge badge-danger" style="margin-left:6px">{{ $totalOverdue }}</span>@endif
  </div>
  <div class="mf-tab {{ $totalOverdue === 0 ? 'active' : '' }}" onclick="switchTab('due',this)">
    Due This Month
    @if($totalDue > 0)<span class="badge badge-warning" style="margin-left:6px">{{ $totalDue }}</span>@endif
  </div>
  <div class="mf-tab" onclick="switchTab('upcoming',this)">
    Upcoming
    @if($totalUpcoming > 0)<span class="badge" style="margin-left:6px">{{ $totalUpcoming }}</span>@endif
  </div>
</div>

{{-- OVERDUE --}}
<div class="mf-section {{ $totalOverdue > 0 ? 'active' : '' }}" id="tab-overdue">
  <div class="gt-card">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title" style="color:#dc2626">Overdue Payments</div>
        <div class="text-xs text-muted">These students have missed their due date + grace period. Late fee is being charged daily.</div>
      </div>
    </div>

    @if($overdueList->isEmpty())
      <div class="gt-empty"><div class="gt-empty-title">No overdue students</div></div>
    @else
      @foreach($overdueList as $book)
      @php $photo = $book->student->profile?->photo ?? 'images/user.png'; @endphp
      <div class="mf-card">
        <img src="{{ asset($photo) }}" class="stu-avatar" alt="photo"
             onerror="this.src='{{ asset('images/user.svg') }}'">
        <div>
          <div class="stu-name">{{ $book->student->profile?->name ?? $book->student->user_id }}</div>
          <div class="stu-sub">{{ $book->course?->name }} &middot; {{ $book->enrollment_no }}</div>
          <div class="stu-sub" style="margin-top:4px;display:flex;gap:6px;flex-wrap:wrap">
            <span class="amount-chip chip-overdue">
              Monthly ₹{{ number_format($book->monthly_amount, 2) }}
            </span>
            @if($book->late_fee_amt > 0)
            <span class="amount-chip chip-overdue">
              Late Fee ₹{{ number_format($book->late_fee_amt, 2) }}
              ({{ $book->overdue_days }} day{{ $book->overdue_days > 1 ? 's' : '' }})
            </span>
            @endif
            <span class="amount-chip chip-overdue">
              Total Due ₹{{ number_format($book->total_due_now, 2) }}
            </span>
          </div>
          <div class="stu-sub" style="color:#dc2626;margin-top:3px">
            Due was {{ $book->next_due?->format('d M Y') ?? '—' }}
            &middot; Grace ended {{ $book->grace_end?->format('d M Y') ?? '—' }}
          </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end">
          <button type="button" class="btn btn-primary btn-sm"
            onclick="openPayModal(
              '{{ $book->id }}',
              '{{ addslashes($book->student->profile?->name ?? $book->student->user_id) }}',
              '{{ number_format($book->total_due_now, 2) }}',
              '{{ $book->total_due_now }}',
              'MONTHLY'
            )">
            Collect ₹{{ number_format($book->total_due_now, 2) }}
          </button>
          <a href="{{ route('institute.enrollment.payment-complete', $book) }}" class="btn btn-outline btn-sm">
            View Detail
          </a>
        </div>
      </div>
      @endforeach
    @endif
  </div>
</div>

{{-- DUE THIS MONTH --}}
<div class="mf-section {{ $totalOverdue === 0 ? 'active' : '' }}" id="tab-due">
  <div class="gt-card">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title">Due This Month — {{ $currentMonth }}</div>
        <div class="text-xs text-muted">Monthly fee is due for these students. Collect before grace period ends.</div>
      </div>
    </div>

    @if($dueList->isEmpty())
      <div class="gt-empty">
        <div class="gt-empty-title">No fees due this month</div>
        <div class="gt-empty-sub">All monthly students are up to date for {{ $currentMonth }}.</div>
      </div>
    @else
      @foreach($dueList as $book)
      @php $photo = $book->student->profile?->photo ?? 'images/user.png'; @endphp
      <div class="mf-card">
        <img src="{{ asset($photo) }}" class="stu-avatar" alt="photo"
             onerror="this.src='{{ asset('images/user.svg') }}'">
        <div>
          <div class="stu-name">{{ $book->student->profile?->name ?? $book->student->user_id }}</div>
          <div class="stu-sub">{{ $book->course?->name }} &middot; {{ $book->enrollment_no }}</div>
          <div class="stu-sub" style="margin-top:4px;display:flex;gap:6px;flex-wrap:wrap">
            <span class="amount-chip chip-due">
              Monthly ₹{{ number_format($book->monthly_amount, 2) }}
            </span>
            @if($book->grace_end)
            <span class="amount-chip chip-due">
              Grace till {{ $book->grace_end->format('d M') }}
            </span>
            @endif
          </div>
          <div class="stu-sub" style="margin-top:3px">
            Due date: {{ $book->next_due?->format('d M Y') ?? '—' }}
          </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end">
          <button type="button" class="btn btn-primary btn-sm"
            onclick="openPayModal(
              '{{ $book->id }}',
              '{{ addslashes($book->student->profile?->name ?? $book->student->user_id) }}',
              '{{ number_format($book->monthly_amount, 2) }}',
              '{{ $book->monthly_amount }}',
              'MONTHLY'
            )">
            Collect ₹{{ number_format($book->monthly_amount, 2) }}
          </button>
          <a href="{{ route('institute.enrollment.payment-complete', $book) }}" class="btn btn-outline btn-sm">
            View Detail
          </a>
        </div>
      </div>
      @endforeach
    @endif
  </div>
</div>

{{-- UPCOMING --}}
<div class="mf-section" id="tab-upcoming">
  <div class="gt-card">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title">Upcoming / Current Month Paid</div>
        <div class="text-xs text-muted">Next due date is in a future month.</div>
      </div>
    </div>

    @if($upcomingList->isEmpty())
      <div class="gt-empty"><div class="gt-empty-title">No upcoming students</div></div>
    @else
      @foreach($upcomingList as $book)
      @php $photo = $book->student->profile?->photo ?? 'images/user.png'; @endphp
      <div class="mf-card">
        <img src="{{ asset($photo) }}" class="stu-avatar" alt="photo"
             onerror="this.src='{{ asset('images/user.svg') }}'">
        <div>
          <div class="stu-name">{{ $book->student->profile?->name ?? $book->student->user_id }}</div>
          <div class="stu-sub">{{ $book->course?->name }} &middot; {{ $book->enrollment_no }}</div>
          <div class="stu-sub" style="margin-top:4px">
            <span class="amount-chip chip-ok">
              Next Due {{ $book->next_due?->format('d M Y') ?? '—' }} &middot; ₹{{ number_format($book->monthly_amount, 2) }}
            </span>
          </div>
        </div>
        <a href="{{ route('institute.enrollment.payment-complete', $book) }}" class="btn btn-outline btn-sm">
          View Detail
        </a>
      </div>
      @endforeach
    @endif
  </div>
</div>

{{-- Pay Modal --}}
<div class="modal-bg" id="pay-modal">
  <div class="modal-box">
    <div class="modal-title">Collect Monthly Fee</div>
    <div class="modal-sub" id="pay-modal-sub"></div>
    <form method="POST" id="pay-form">
      @csrf
      <div class="gt-form-group">
        <label class="gt-label">Amount (₹) <span style="color:var(--danger)">*</span></label>
        <input type="number" name="amount" id="pay-amount-input" class="gt-input"
               step="0.01" min="0.01" required>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Payment Mode <span style="color:var(--danger)">*</span></label>
        <select name="payment_mode" class="gt-select" required>
          <option value="">Select Mode</option>
          <option value="CASH">Cash</option>
          <option value="UPI">UPI</option>
          <option value="NEFT">NEFT</option>
          <option value="IMPS">IMPS</option>
          <option value="CHEQUE">Cheque</option>
        </select>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="gt-form-group">
          <label class="gt-label">Payment Date <span style="color:var(--danger)">*</span></label>
          <input type="date" name="payment_date" class="gt-input" value="{{ $today->toDateString() }}" required>
        </div>
        <div class="gt-form-group">
          <label class="gt-label">UTR / Ref</label>
          <input type="text" name="utr" class="gt-input" placeholder="Optional">
        </div>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Note</label>
        <input type="text" name="payment_note" class="gt-input"
               placeholder="{{ $today->format('F Y') }} monthly fee">
      </div>
      <div style="display:flex;gap:10px;margin-top:8px">
        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">Record Payment</button>
        <button type="button" class="btn btn-outline" onclick="closePayModal()">Cancel</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(id, el) {
  document.querySelectorAll('.mf-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.mf-section').forEach(s => s.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('tab-' + id).classList.add('active');
}

const _payBase = '{{ url("dashboard/enrollment") }}';

function openPayModal(bookId, name, displayAmt, rawAmt, planType) {
  document.getElementById('pay-form').action = _payBase + '/' + bookId + '/payment';
  document.getElementById('pay-modal-sub').textContent = name + ' — ₹' + displayAmt;
  document.getElementById('pay-amount-input').value = parseFloat(rawAmt).toFixed(2);
  document.getElementById('pay-modal').classList.add('open');
}
function closePayModal() { document.getElementById('pay-modal').classList.remove('open'); }

document.getElementById('pay-modal').addEventListener('click', function(e) {
  if (e.target === this) this.classList.remove('open');
});
</script>
@endpush
