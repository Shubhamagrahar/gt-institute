@extends('layouts.institute')
@section('title', 'Payment Complete')
@section('page-title', 'Payment & Admission')

@section('content')
@php
  $amountColumn = \App\Models\FeeCollectDetail::amountColumn();
  $isAdmitted = $courseBook->status === 'RUN';
@endphp

{{-- Status Hero --}}
<div style="background:linear-gradient(135deg,{{ $isAdmitted ? '#064e3b,#059669' : '#1e3a5f,#2563eb' }});color:#fff;border-radius:20px;padding:24px 28px;margin-bottom:18px;">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;">
    <div>
      <div style="display:inline-flex;align-items:center;padding:5px 12px;border-radius:999px;background:rgba(255,255,255,.18);font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;margin-bottom:10px;">
        {{ $isAdmitted ? 'Admission Confirmed' : 'Seat Booked — Payment Received' }}
      </div>
      <div style="font-size:26px;font-weight:900;line-height:1.2;">
        {{ $isAdmitted ? ($courseBook->enrollment_no ?? 'Enrollment Generated') : ($courseBook->student->profile?->name ?? $courseBook->student->user_id) }}
      </div>
      @if($isAdmitted)
        <div style="opacity:.85;margin-top:4px;">{{ $courseBook->student->profile?->name ?? $courseBook->student->user_id }}</div>
      @endif
      <div style="opacity:.85;margin-top:4px;">{{ $courseBook->course->name }}@if($courseBook->batch) &middot; {{ $courseBook->batch->name }}@endif</div>
    </div>
    <div style="text-align:right;">
      <div style="font-size:11px;opacity:.75;text-transform:uppercase;letter-spacing:.08em;">Total Paid</div>
      <div style="font-size:32px;font-weight:900;">₹{{ number_format($paidTotal, 2) }}</div>
      <div style="font-size:12px;opacity:.75;">of ₹{{ number_format($courseBook->final_fee, 2) }} total fee</div>
    </div>
  </div>
</div>

{{-- Info grid --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">
  <div class="gt-card">
    <div class="gt-card-header" style="padding-bottom:0;">
      <div class="gt-card-title">Student Info</div>
    </div>
    <div class="gt-card-body" style="padding:14px 18px;">
      <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;">
        <div style="display:flex;justify-content:space-between;"><span class="text-muted">Name</span><strong>{{ $courseBook->student->profile?->name ?? 'N/A' }}</strong></div>
        <div style="display:flex;justify-content:space-between;"><span class="text-muted">Mobile</span><strong>{{ $courseBook->student->mobile }}</strong></div>
        @if($courseBook->student->email)
        <div style="display:flex;justify-content:space-between;"><span class="text-muted">Email</span><strong>{{ $courseBook->student->email }}</strong></div>
        @endif
        <div style="display:flex;justify-content:space-between;"><span class="text-muted">Enrollment No.</span>
          <strong class="mono">{{ $courseBook->enrollment_no ?? 'Pending payment completion' }}</strong>
        </div>
        <div style="display:flex;justify-content:space-between;"><span class="text-muted">Course</span><strong>{{ $courseBook->course->name }}</strong></div>
        <div style="display:flex;justify-content:space-between;"><span class="text-muted">Status</span>
          <span class="badge {{ $isAdmitted ? 'badge-success' : 'badge-warning' }}">{{ $isAdmitted ? 'ADMITTED' : 'SEAT BOOKED' }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="gt-card">
    <div class="gt-card-header" style="padding-bottom:0;">
      <div class="gt-card-title">Payment Summary</div>
    </div>
    <div class="gt-card-body" style="padding:14px 18px;">
      @php $due = max($courseBook->final_fee - $paidTotal, 0); @endphp
      <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;">
        <div style="display:flex;justify-content:space-between;"><span class="text-muted">Plan Type</span><strong>{{ $courseBook->paymentPlan?->plan_type ?? 'N/A' }}</strong></div>
        <div style="display:flex;justify-content:space-between;"><span class="text-muted">Total Course Fee</span><strong class="mono">₹{{ number_format($courseBook->final_fee, 2) }}</strong></div>
        <div style="display:flex;justify-content:space-between;"><span class="text-muted">Total Paid</span><strong class="mono" style="color:#16a34a;">₹{{ number_format($paidTotal, 2) }}</strong></div>
        <div style="display:flex;justify-content:space-between;"><span class="text-muted">Balance Due</span>
          <strong class="mono" style="color:{{ $due > 0 ? '#dc2626' : '#16a34a' }};">₹{{ number_format($due, 2) }}</strong>
        </div>
      </div>
      @if(!$isAdmitted && $due > 0)
        <div class="gt-alert gt-alert-warning" style="margin-top:12px;font-size:12px;">
          Remaining ₹{{ number_format($due, 2) }} due. Collect balance payment to finalize admission.
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Latest Receipt --}}
@if($latestFee)
<div class="gt-card" style="margin-bottom:18px;">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">Latest Receipt</div>
      <div class="text-xs text-muted">Invoice: {{ $latestFee->invoice_no }} &middot; {{ $latestFee->payment_mode }} &middot; {{ $latestFee->date->format('d M Y') }}</div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <a href="{{ route('institute.enrollment.receipt.a4', [$courseBook, $latestFee]) }}" target="_blank" class="btn btn-outline btn-sm">
        A4 Receipt
      </a>
      <a href="{{ route('institute.enrollment.receipt.thermal', [$courseBook, $latestFee]) }}" target="_blank" class="btn btn-outline btn-sm">
        Thermal Receipt
      </a>
    </div>
  </div>
  <div style="padding:14px 18px;display:grid;grid-template-columns:repeat(4,1fr);gap:12px;font-size:13px;">
    <div><div class="text-xs text-muted" style="margin-bottom:2px;">Amount</div><div class="fw-600 mono" style="font-size:16px;">₹{{ number_format($latestFee->{$amountColumn}, 2) }}</div></div>
    <div><div class="text-xs text-muted" style="margin-bottom:2px;">Mode</div><div class="fw-600">{{ $latestFee->payment_mode }}</div></div>
    <div><div class="text-xs text-muted" style="margin-bottom:2px;">Date</div><div class="fw-600">{{ $latestFee->date->format('d M Y') }}</div></div>
    @if($latestFee->utr)
    <div><div class="text-xs text-muted" style="margin-bottom:2px;">UTR/Ref</div><div class="fw-600 mono">{{ $latestFee->utr }}</div></div>
    @endif
  </div>
</div>
@endif

{{-- All Payments Table --}}
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Payments</div>
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Invoice</th>
          <th>Date</th>
          <th>Mode</th>
          <th>Amount</th>
          <th>UTR / Ref</th>
          <th>Receipt</th>
        </tr>
      </thead>
      <tbody>
        @forelse($fees as $f)
          <tr>
            <td class="text-muted">{{ $fees->firstItem() + $loop->index }}</td>
            <td class="mono">{{ $f->invoice_no }}</td>
            <td>{{ $f->date->format('d M Y') }}</td>
            <td><span class="badge">{{ $f->payment_mode }}</span></td>
            <td class="mono fw-600">₹{{ number_format($f->{$amountColumn}, 2) }}</td>
            <td class="mono text-muted">{{ $f->utr ?: '-' }}</td>
            <td>
              <div style="display:flex;gap:6px;">
                <a href="{{ route('institute.enrollment.receipt.a4', [$courseBook, $f]) }}" target="_blank" class="btn btn-outline btn-sm" style="padding:3px 10px;font-size:11px;">A4</a>
                <a href="{{ route('institute.enrollment.receipt.thermal', [$courseBook, $f]) }}" target="_blank" class="btn btn-outline btn-sm" style="padding:3px 10px;font-size:11px;">Thermal</a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align:center;color:var(--text-2);padding:24px;">No payments recorded yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($fees->hasPages())
    <div style="padding:12px 18px;">{{ $fees->links() }}</div>
  @endif
</div>

{{-- Actions --}}
<div style="display:flex;gap:10px;margin-top:18px;justify-content:flex-end;flex-wrap:wrap;">
  <a href="{{ route('institute.enrollment.pending') }}" class="btn btn-outline">Back to Pending</a>
  <a href="{{ route('institute.enrollment.fee', $courseBook) }}" class="btn btn-outline">Collect More Payment</a>
  <a href="{{ route('institute.students.show', $courseBook->student) }}" class="btn btn-primary">View Student Profile</a>
</div>
@endsection
