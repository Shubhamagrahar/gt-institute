@extends('layouts.institute')
@section('title', 'Fee Collection')
@section('page-title', 'Fee Collection')

@push('styles')
<style>
.fc-hero{background:linear-gradient(135deg,#0f172a,#1e3a5f);color:#fff;border-radius:20px;padding:22px 26px;margin-bottom:20px}
.fc-hero-admitted{background:linear-gradient(135deg,#064e3b,#047857)}
.fc-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:999px;background:rgba(255,255,255,.15);font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;margin-bottom:10px}
.fc-hero-name{font-size:26px;font-weight:900;line-height:1.2}
.fc-hero-sub{opacity:.8;margin-top:4px;font-size:13px}
.fc-paid-big{font-size:34px;font-weight:900}
.fc-paid-label{font-size:11px;opacity:.7;text-transform:uppercase;letter-spacing:.08em}

.fc-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
@media(max-width:768px){.fc-grid{grid-template-columns:1fr}}

.fc-info-row{display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid var(--border);font-size:13px}
.fc-info-row:last-child{border-bottom:none}

.tbl{width:100%;border-collapse:collapse;font-size:13px}
.tbl th{background:var(--bg-3);padding:10px 12px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-2);white-space:nowrap}
.tbl td{padding:10px 12px;border-bottom:1px solid var(--border);vertical-align:middle}
.tbl tr:last-child td{border-bottom:none}
.tbl tbody tr.cancelled-row td:not(.no-strike){text-decoration:line-through;opacity:.5}

.badge-mode{display:inline-block;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;background:var(--bg-3);color:var(--text-1)}
.badge-cancelled{background:#fef2f2;color:#b91c1c;display:inline-block;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700}
.badge-active{background:#f0fdf4;color:#15803d;display:inline-block;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700}
.btn-xs{padding:3px 10px;font-size:11px;border-radius:6px}

/* Ledger colors */
.dr{color:#dc2626;font-weight:700}
.cr{color:#16a34a;font-weight:700}

/* Modals */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:9999;align-items:center;justify-content:center;padding:16px}
.modal-bg.open{display:flex}
.modal-box{background:#ffffff;border-radius:18px;padding:28px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 60px rgba(0,0,0,.35)}
.modal-title{font-size:18px;font-weight:800;margin-bottom:4px;color:#111827}
.modal-sub{font-size:13px;color:#6b7280;margin-bottom:16px}
.modal-due-box{background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:18px;display:flex;justify-content:space-between;align-items:center}
.modal-due-label{font-size:12px;font-weight:600;color:#b91c1c;text-transform:uppercase;letter-spacing:.05em}
.modal-due-amount{font-size:22px;font-weight:900;color:#dc2626}
@keyframes spin{to{transform:rotate(360deg)}}
.btn-spinner{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;vertical-align:middle;margin-right:6px}
</style>
@endpush

@section('content')
@php
  $amountColumn = \App\Models\FeeCollectDetail::amountColumn();
  $isAdmitted   = $courseBook->status === 'RUN';
  $plan         = $courseBook->paymentPlan;
@endphp

{{-- Hero --}}
<div class="fc-hero {{ $isAdmitted ? 'fc-hero-admitted' : '' }}">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px">
    <div>
      <div class="fc-badge">{{ $isAdmitted ? '✓ Admitted' : '◉ Seat Booked' }}</div>
      <div class="fc-hero-name">
        {{ $isAdmitted ? ($courseBook->enrollment_no ?? '—') : ($courseBook->student->profile?->name ?? $courseBook->student->user_id) }}
      </div>
      @if($isAdmitted)
        <div class="fc-hero-sub">{{ $courseBook->student->profile?->name ?? $courseBook->student->user_id }}</div>
      @endif
      <div class="fc-hero-sub" style="margin-top:6px">
        {{ $courseBook->course->name }}
        @if($courseBook->batch) &middot; {{ $courseBook->batch->name }}@endif
        &middot; <strong>{{ $plan?->plan_type ?? 'No Plan' }}</strong>
      </div>
    </div>
    <div style="text-align:right">
      <div class="fc-paid-label">Collected</div>
      <div class="fc-paid-big">₹{{ number_format($paidTotal, 2) }}</div>
      <div style="font-size:12px;opacity:.7">of ₹{{ number_format($courseBook->final_fee, 2) }}</div>
      @if($due > 0)
        <div style="margin-top:6px;background:rgba(239,68,68,.25);padding:3px 12px;border-radius:8px;font-size:12px">
          ₹{{ number_format($due, 2) }} due
          @if($lateFee > 0) + ₹{{ number_format($lateFee, 2) }} late fee @endif
        </div>
      @else
        <div style="margin-top:6px;background:rgba(16,185,129,.25);padding:3px 12px;border-radius:8px;font-size:12px">Fully Paid</div>
      @endif
    </div>
  </div>
</div>

@if(session('success'))
  <div class="gt-alert gt-alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
@endif

{{-- Info Grid --}}
<div class="fc-grid">
  <div class="gt-card">
    <div class="gt-card-header"><div class="gt-card-title">Student Info</div></div>
    <div class="gt-card-body" style="padding:14px 18px">
      <div class="fc-info-row"><span class="text-muted">Name</span><strong>{{ $courseBook->student->profile?->name ?? 'N/A' }}</strong></div>
      <div class="fc-info-row"><span class="text-muted">Mobile</span><strong>{{ $courseBook->student->mobile }}</strong></div>
      <div class="fc-info-row"><span class="text-muted">Enrollment No.</span>
        <strong class="mono">{{ $courseBook->enrollment_no ?? '— pending' }}</strong>
      </div>
      <div class="fc-info-row"><span class="text-muted">Status</span>
        <span class="badge {{ $isAdmitted ? 'badge-success' : 'badge-warning' }}">
          {{ $isAdmitted ? 'ADMITTED' : 'SEAT BOOKED' }}
        </span>
      </div>
    </div>
  </div>

  <div class="gt-card">
    <div class="gt-card-header"><div class="gt-card-title">Payment Summary</div></div>
    <div class="gt-card-body" style="padding:14px 18px">
      <div class="fc-info-row"><span class="text-muted">Plan</span><strong>{{ $plan?->plan_type ?? 'N/A' }}</strong></div>
      <div class="fc-info-row"><span class="text-muted">Total Fee</span><strong class="mono">₹{{ number_format($courseBook->final_fee, 2) }}</strong></div>
      @if($plan?->plan_type === 'MONTHLY' && $plan->monthly_amount)
      <div class="fc-info-row"><span class="text-muted">Monthly Amount</span><strong class="mono">₹{{ number_format($plan->monthly_amount, 2) }}</strong></div>
      @endif
      <div class="fc-info-row"><span class="text-muted">Collected</span>
        <strong class="mono" style="color:#16a34a">₹{{ number_format($paidTotal, 2) }}</strong>
      </div>
      <div class="fc-info-row"><span class="text-muted">Balance Due</span>
        <strong class="mono" style="color:{{ $due > 0 ? '#dc2626' : '#16a34a' }}">₹{{ number_format($due, 2) }}</strong>
      </div>
      @if($lateFee > 0)
      <div class="fc-info-row"><span class="text-muted" style="color:#dc2626">Late Fee</span>
        <strong class="mono dr">+₹{{ number_format($lateFee, 2) }}</strong>
      </div>
      @endif
      @if($plan?->next_due_date)
      <div class="fc-info-row"><span class="text-muted">Next Due</span>
        <strong>{{ \Carbon\Carbon::parse($plan->next_due_date)->format('d M Y') }}</strong>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- Actions --}}
<div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap">
  @if($plan)
    <button type="button" class="btn btn-primary" onclick="openPayModal()">
      + Collect Payment
    </button>
  @else
    <a href="{{ route('institute.enrollment.fee', $courseBook) }}" class="btn btn-primary">
      Setup Payment Plan
    </a>
  @endif
  <a href="{{ route('institute.enrollment.profile', $courseBook) }}" class="btn btn-outline">Edit Profile</a>
  <a href="{{ route('institute.students.show', $courseBook->student) }}" class="btn btn-outline">Student Profile</a>
  <a href="{{ route('institute.enrollment.pending') }}" class="btn btn-outline">← Back</a>
</div>

{{-- Receipts Table --}}
<div class="gt-card" style="margin-bottom:20px">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">Payment Receipts</div>
      <div class="text-xs text-muted">Cancelled entries shown in strikethrough</div>
    </div>
  </div>
  <div style="overflow-x:auto">
    <table class="tbl">
      <thead>
        <tr>
          <th>#</th>
          <th>Invoice</th>
          <th>Date</th>
          <th>Mode</th>
          <th>Amount</th>
          <th>UTR / Ref</th>
          <th>Status</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($fees as $f)
        <tr class="{{ $f->isCancelled() ? 'cancelled-row' : '' }}">
          <td class="text-muted">{{ $fees->firstItem() + $loop->index }}</td>
          <td class="mono">{{ $f->invoice_no }}</td>
          <td style="white-space:nowrap">{{ $f->date->format('d M Y') }}</td>
          <td><span class="badge-mode">{{ $f->payment_mode }}</span></td>
          <td class="mono fw-600">₹{{ number_format($f->{$amountColumn}, 2) }}</td>
          <td class="mono text-muted">{{ $f->utr ?: '—' }}</td>
          <td class="no-strike">
            @if($f->isCancelled())
              <span class="badge-cancelled">CANCELLED</span>
            @else
              <span class="badge-active">ACTIVE</span>
            @endif
          </td>
          <td class="no-strike" style="text-align:right">
            <div style="display:flex;gap:5px;justify-content:flex-end;flex-wrap:wrap">
              @if(!$f->isCancelled())
                <a href="{{ route('institute.enrollment.receipt.a4', [$courseBook, $f]) }}" target="_blank"
                   class="btn btn-outline btn-xs">A4</a>
                <a href="{{ route('institute.enrollment.receipt.thermal', [$courseBook, $f]) }}" target="_blank"
                   class="btn btn-outline btn-xs">Thermal</a>
                <button type="button" class="btn btn-xs"
                  style="background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5"
                  onclick="openCancelModal('{{ $f->id }}','{{ $f->invoice_no }}','{{ number_format($f->{$amountColumn},2) }}')">
                  Cancel
                </button>
              @else
                <span class="text-muted" style="font-size:11px;max-width:120px;display:block;word-break:break-word">
                  {{ \Illuminate\Support\Str::limit($f->cancel_reason, 30) }}
                </span>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" style="text-align:center;color:var(--text-2);padding:28px">No payments recorded yet.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($fees->hasPages())
    <div style="padding:12px 18px">{{ $fees->links() }}</div>
  @endif
</div>

{{-- Transaction Ledger --}}
@if($transactions->count())
<div class="gt-card">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">Transaction Ledger</div>
      <div class="text-xs text-muted">Complete debit / credit history</div>
    </div>
  </div>
  <div style="overflow-x:auto">
    <table class="tbl">
      <thead>
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Description</th>
          <th style="text-align:right">Debit (Dr)</th>
          <th style="text-align:right">Credit (Cr)</th>
          <th style="text-align:right">Opening Bal</th>
          <th style="text-align:right">Closing Bal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($transactions as $txn)
        <tr>
          <td class="text-muted">{{ $loop->iteration }}</td>
          <td style="white-space:nowrap">{{ \Carbon\Carbon::parse($txn->date)->format('d M Y') }}</td>
          <td style="max-width:260px;word-break:break-word;font-size:12px">{{ $txn->description }}</td>
          <td style="text-align:right" class="mono {{ $txn->debit > 0 ? 'dr' : 'text-muted' }}">
            {{ $txn->debit > 0 ? '₹'.number_format($txn->debit, 2) : '—' }}
          </td>
          <td style="text-align:right" class="mono {{ $txn->credit > 0 ? 'cr' : 'text-muted' }}">
            {{ $txn->credit > 0 ? '₹'.number_format($txn->credit, 2) : '—' }}
          </td>
          <td style="text-align:right" class="mono text-muted" style="font-size:12px">
            ₹{{ number_format($txn->op_bal, 2) }}
          </td>
          <td style="text-align:right" class="mono fw-600"
              style="{{ (float)$txn->cl_bal < 0 ? 'color:#dc2626' : '' }}">
            ₹{{ number_format($txn->cl_bal, 2) }}
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="background:var(--bg-3)">
          <td colspan="3" style="padding:10px 12px;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--text-2)">Totals</td>
          <td style="text-align:right;padding:10px 12px" class="mono dr">₹{{ number_format($transactions->sum('debit'), 2) }}</td>
          <td style="text-align:right;padding:10px 12px" class="mono cr">₹{{ number_format($transactions->sum('credit'), 2) }}</td>
          <td></td>
          <td style="text-align:right;padding:10px 12px" class="mono fw-600">
            ₹{{ number_format($transactions->last()->cl_bal ?? 0, 2) }}
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endif

{{-- Pay Modal --}}
<div class="modal-bg" id="pay-modal">
  <div class="modal-box">
    <div class="modal-title">Collect Payment</div>
    <div class="modal-sub">
      {{ $courseBook->student->profile?->name ?? $courseBook->student->user_id }}
      &middot; {{ $plan?->plan_type ?? 'PART' }}
    </div>

    {{-- Due Amount Banner --}}
    @if($due > 0 || $lateFee > 0)
    <div class="modal-due-box">
      <div>
        <div class="modal-due-label">Amount Due</div>
        @if($lateFee > 0)
          <div style="font-size:11px;color:#b91c1c;margin-top:2px">Includes ₹{{ number_format($lateFee,2) }} late fee</div>
        @endif
      </div>
      <div class="modal-due-amount">₹{{ number_format($due + $lateFee, 2) }}</div>
    </div>
    @else
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:10px 16px;margin-bottom:18px;font-size:13px;font-weight:700;color:#15803d">
      Fully Paid — Recording extra/advance payment
    </div>
    @endif

    <form id="pay-form" method="POST" action="{{ route('institute.enrollment.add-payment', $courseBook) }}">
      @csrf
      <div class="gt-form-group">
        <label class="gt-label">Amount (₹) <span style="color:#dc2626">*</span></label>
        <input type="number" name="amount" id="pay-amount-input" class="gt-input"
               step="0.01" min="0.01"
               value="{{ $modalDefaultAmount ?? '' }}"
               placeholder="{{ $plan?->plan_type === 'PART' ? 'Enter amount' : number_format($modalDefaultAmount ?? 0, 2) }}"
               required>
        @if($lateFee > 0)
          <div style="margin-top:4px;font-size:12px;color:#dc2626">
            Includes ₹{{ number_format($lateFee, 2) }} late fee
          </div>
        @endif
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Payment Mode <span style="color:#dc2626">*</span></label>
        <select name="payment_mode" id="pay-mode-select" class="gt-select" required>
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
          <label class="gt-label">Payment Date <span style="color:#dc2626">*</span></label>
          <input type="date" name="payment_date" class="gt-input" value="{{ now()->toDateString() }}" required>
        </div>
        <div class="gt-form-group">
          <label class="gt-label" id="utr-label">UTR / Reference</label>
          <input type="text" name="utr" id="pay-utr-input" class="gt-input" placeholder="Optional">
        </div>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Note</label>
        <input type="text" name="payment_note" class="gt-input" placeholder="Optional note">
      </div>
      <div style="display:flex;gap:10px;margin-top:18px">
        <button type="submit" id="pay-submit-btn" class="btn btn-primary" style="flex:1;justify-content:center">
          Record Payment
        </button>
        <button type="button" class="btn btn-outline" onclick="closePayModal()">Cancel</button>
      </div>
    </form>
  </div>
</div>

{{-- Cancel Modal --}}
<div class="modal-bg" id="cancel-modal">
  <div class="modal-box">
    <div class="modal-title" style="color:#b91c1c">Cancel Payment</div>
    <div class="modal-sub" id="cancel-modal-sub"></div>
    <form method="POST" id="cancel-form">
      @csrf
      <div class="gt-form-group">
        <label class="gt-label">Reason <span style="color:var(--danger)">*</span></label>
        <textarea name="reason" class="gt-input" rows="3" required
                  placeholder="Describe why this payment is being cancelled..."></textarea>
      </div>
      <div class="gt-form-group">
        <label class="gt-label">Type <strong>CANCEL</strong> to confirm <span style="color:var(--danger)">*</span></label>
        <input type="text" name="confirm" class="gt-input" placeholder="CANCEL"
               autocomplete="off" required style="font-family:monospace;font-weight:700;letter-spacing:.12em">
      </div>
      <div style="display:flex;gap:10px;margin-top:8px">
        <button type="submit" class="btn" style="background:#dc2626;color:#fff;flex:1;justify-content:center">
          Confirm Cancel
        </button>
        <button type="button" class="btn btn-outline" onclick="closeCancelModal()">Back</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Pay modal
function openPayModal()  {
  document.getElementById('pay-modal').classList.add('open');
  // Reset submit button in case modal was closed mid-submit
  const btn = document.getElementById('pay-submit-btn');
  btn.disabled = false;
  btn.innerHTML = 'Record Payment';
}
function closePayModal() { document.getElementById('pay-modal').classList.remove('open'); }

// UTR required toggle based on payment mode
const _modeSelect = document.getElementById('pay-mode-select');
const _utrInput   = document.getElementById('pay-utr-input');
const _utrLabel   = document.getElementById('utr-label');
const _utrRequiredModes = ['UPI','NEFT','IMPS','CHEQUE'];

_modeSelect && _modeSelect.addEventListener('change', function () {
  const needsUtr = _utrRequiredModes.includes(this.value);
  _utrInput.required = needsUtr;
  _utrInput.placeholder = needsUtr ? ('Required for ' + this.value) : 'Optional';
  _utrLabel.innerHTML = needsUtr
    ? 'UTR / Reference <span style="color:#dc2626">*</span>'
    : 'UTR / Reference';
});

// Spinner on submit to prevent duplicate entry
document.getElementById('pay-form') && document.getElementById('pay-form').addEventListener('submit', function () {
  const btn = document.getElementById('pay-submit-btn');
  btn.disabled = true;
  btn.innerHTML = '<span class="btn-spinner"></span> Processing…';
});

// Cancel modal
const _cancelBase = '{{ route("institute.enrollment.receipt.cancel", [$courseBook, "__ID__"]) }}';
function openCancelModal(feeId, invoiceNo, amount) {
  document.getElementById('cancel-form').action = _cancelBase.replace('__ID__', feeId);
  document.getElementById('cancel-modal-sub').textContent = 'Invoice: ' + invoiceNo + ' — ₹' + amount;
  document.getElementById('cancel-modal').classList.add('open');
}
function closeCancelModal() { document.getElementById('cancel-modal').classList.remove('open'); }

// Close on backdrop click
['pay-modal','cancel-modal'].forEach(id => {
  document.getElementById(id).addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
  });
});
</script>
@endpush
