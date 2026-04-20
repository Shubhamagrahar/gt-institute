@extends('layouts.institute')
@section('title','Payment')
@section('page-title','Payment Setup')

@push('styles')
<style>
.pay-shell{max-width:1180px;margin:0 auto;display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:20px;align-items:start}
.pay-card{background:var(--bg-2);border:1px solid var(--border);border-radius:18px;overflow:hidden}.pay-head{background:linear-gradient(135deg,#6651d8,#503ab9);color:#fff;padding:20px 24px}
.pay-head h2{margin:0;font-size:22px}.pay-head p{margin:5px 0 0;opacity:.82}.pay-body{padding:22px}
.type-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px}.type-card{border:1px solid var(--border);background:var(--bg-3);border-radius:16px;padding:16px;cursor:pointer;transition:.2s;position:relative}
.type-card.active{border-color:#6c5dd3;box-shadow:0 0 0 3px rgba(108,93,211,.18);background:rgba(108,93,211,.12)}.type-code{font-size:24px;font-weight:950;color:#a89cf5}.type-name{font-weight:800;margin:4px 0}.type-desc{font-size:12px;color:var(--text-2);line-height:1.5}
.fee-row{background:var(--bg-3);border:1px solid var(--border);border-radius:14px;padding:14px;margin-bottom:10px}.fee-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:14px;align-items:end}
.summary-card{position:sticky;top:18px;background:linear-gradient(180deg,#f7f8ff,#fff);border:1px solid #dfe6ff;border-radius:18px;padding:20px;color:#1f2937;box-shadow:0 18px 44px rgba(15,23,42,.09)}
.summary-title{font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:.1em;font-weight:900}.summary-total{font-size:34px;font-weight:950;color:#503ab9;margin:6px 0 14px}.summary-line{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #e5e7eb;font-size:13px}.pay-help{display:none;margin-top:14px;background:#eef4ff;border:1px solid #dbe6ff;color:#475569;border-radius:14px;padding:14px;font-size:13px;line-height:1.6}
@media(max-width:1020px){.pay-shell{grid-template-columns:1fr}.summary-card{position:static}.type-grid{grid-template-columns:1fr}.fee-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
@php
  $planByType = $plans->keyBy('type');
  $defaultPlan = $planByType['OTP'] ?? $plans->first();
  $activeType = old('payment_type', $defaultPlan?->type ?? 'OTP');
@endphp

<form method="POST" action="{{ route('institute.enrollment.save-fee', $courseBook) }}" id="fee-form">
  @csrf
  <input type="hidden" name="payment_plan_type_id" id="payment-plan-id" value="{{ old('payment_plan_type_id', $defaultPlan?->id) }}">

  <div class="pay-shell">
    <div class="pay-card">
      <div class="pay-head">
        <h2>{{ $courseBook->student->profile?->name ?? $courseBook->student->user_id }}</h2>
        <p>Configure fee, discount, and payment type for {{ $courseBook->course->name }}.</p>
      </div>
      <div class="pay-body">
        @if($plans->isEmpty())
          <div class="gt-alert gt-alert-warning" style="margin-bottom:16px;">
            Payment type configuration is missing. Add OTP, PART, and MONTH records before submitting this form.
          </div>
        @endif

        <div class="gt-card-title" style="margin-bottom:12px;">Payment Type</div>
        <div class="type-grid">
          <div class="type-card {{ $activeType === 'OTP' ? 'active' : '' }}" data-type="OTP" data-plan-id="{{ $planByType['OTP']->id ?? '' }}">
            <div class="type-code">OTP</div>
            <div class="type-name">One Time Payment</div>
            <div class="type-desc">The student pays the full amount in a single payment.</div>
          </div>
          <div class="type-card {{ $activeType === 'PART' ? 'active' : '' }}" data-type="PART" data-plan-id="{{ $planByType['PART']->id ?? '' }}">
            <div class="type-code">PART</div>
            <div class="type-name">Partial Payment</div>
            <div class="type-desc">Collect an advance amount and track the remaining due amount.</div>
          </div>
          <div class="type-card {{ $activeType === 'MONTHLY' ? 'active' : '' }}" data-type="MONTHLY" data-plan-id="{{ $planByType['MONTHLY']->id ?? '' }}">
            <div class="type-code">MONTH</div>
            <div class="type-name">Monthly Payment</div>
            <div class="type-desc">Monthly installments are calculated from the course duration.</div>
          </div>
        </div>

        <div class="pay-help" id="pay-help"></div>

        <div class="gt-divider"></div>
        <div class="gt-card-title" style="margin-bottom:12px;">Fee & Discount</div>
        <div id="fee-rows">
          @forelse($feeStructure as $fs)
            <div class="fee-row">
              <input type="hidden" name="fee_items[{{ $loop->index }}][fee_type_id]" value="{{ $fs->fee_type_id }}">
              <input type="hidden" name="fee_items[{{ $loop->index }}][fee_type_name]" value="{{ $fs->fee_type_name }}">
              <div class="fee-grid">
                <div class="gt-form-group" style="margin-bottom:0;">
                  <label class="gt-label">Fee Type</label>
                  <input type="text" class="gt-input" value="{{ $fs->fee_type_name }}" readonly>
                </div>
                <div class="gt-form-group" style="margin-bottom:0;">
                  <label class="gt-label">Amount (₹)</label>
                  <input type="number" name="fee_items[{{ $loop->index }}][original_amount]" class="gt-input orig-amt" value="{{ $fs->amount }}" readonly>
                </div>
                <div class="gt-form-group" style="margin-bottom:0;">
                  <label class="gt-label">Discount (%)</label>
                  <input type="number" name="fee_items[{{ $loop->index }}][discount_percent]" class="gt-input disc-pct" value="0" min="0" max="100" step="0.01">
                </div>
                <div class="gt-form-group" style="margin-bottom:0;">
                  <label class="gt-label">Discount (₹)</label>
                  <input type="number" name="fee_items[{{ $loop->index }}][discount_amount]" class="gt-input disc-amt" value="0" min="0" step="0.01" readonly>
                </div>
              </div>
              <input type="hidden" name="fee_items[{{ $loop->index }}][final_amount]" class="final-amt" value="{{ $fs->amount }}">
            </div>
          @empty
            <div class="gt-alert gt-alert-warning">No fee structure defined for this course. Please set up course fees first.</div>
          @endforelse
        </div>
      </div>
    </div>

    <div class="summary-card">
      <div class="summary-title">Payable Summary</div>
      <div class="summary-total" id="grand-total">₹0.00</div>
      <div class="summary-line"><span>Course</span><strong>{{ $courseBook->course->name }}</strong></div>
      <div class="summary-line"><span>Total Discount</span><strong id="total-discount">₹0.00</strong></div>
      <div class="summary-line"><span>Payment Type</span><strong id="summary-type">{{ $activeType === 'MONTHLY' ? 'MONTH' : $activeType }}</strong></div>
      <div class="summary-line" id="monthly-line" style="display:none;"><span>Approx Monthly</span><strong id="monthly-amount">₹0.00</strong></div>
      <button type="submit" class="btn btn-primary w-full" style="justify-content:center;margin-top:18px;" {{ $plans->isEmpty() ? 'disabled' : '' }}>Continue to Preview</button>
      <div class="text-muted text-xs" style="margin-top:12px;line-height:1.5;">This UI currently maps to the existing backend payment configuration to preserve compatibility.</div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
function money(value) {
  return '₹' + (Number(value || 0)).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function recalc() {
  let totalDiscount = 0, grandTotal = 0;
  document.querySelectorAll('.fee-row').forEach(row => {
    const orig = parseFloat(row.querySelector('.orig-amt').value) || 0;
    const pct = parseFloat(row.querySelector('.disc-pct').value) || 0;
    const discAmt = Math.round(orig * pct / 100 * 100) / 100;
    const final = Math.max(0, orig - discAmt);
    row.querySelector('.disc-amt').value = discAmt.toFixed(2);
    row.querySelector('.final-amt').value = final.toFixed(2);
    totalDiscount += discAmt;
    grandTotal += final;
  });
  document.getElementById('total-discount').textContent = money(totalDiscount);
  document.getElementById('grand-total').textContent = money(grandTotal);

  const activeType = document.querySelector('.type-card.active')?.dataset.type || 'OTP';
  const monthlyLine = document.getElementById('monthly-line');
  if (activeType === 'MONTHLY') {
    const duration = {{ $courseBook->course->duration_months ?? $courseBook->course->duration ?? 6 }};
    document.getElementById('monthly-amount').textContent = money(grandTotal / Math.max(1, duration));
    monthlyLine.style.display = '';
  } else {
    monthlyLine.style.display = 'none';
  }
}

document.querySelectorAll('.disc-pct').forEach(el => el.addEventListener('input', recalc));
document.querySelectorAll('.type-card').forEach(card => {
  card.addEventListener('click', () => {
    document.querySelectorAll('.type-card').forEach(item => item.classList.remove('active'));
    card.classList.add('active');
    document.getElementById('payment-plan-id').value = card.dataset.planId || '';
    const label = card.dataset.type === 'MONTHLY' ? 'MONTH' : card.dataset.type;
    document.getElementById('summary-type').textContent = label;
    const help = document.getElementById('pay-help');
    help.style.display = '';
    help.textContent = card.dataset.type === 'OTP'
      ? 'The full amount will be paid in a single payment. No due amount remains.'
      : card.dataset.type === 'PART'
        ? 'A partial amount will be collected and the remaining due amount will be tracked in the student ledger.'
        : 'Installments are previewed based on the course duration.';
    recalc();
  });
});
recalc();
</script>
@endpush
