@extends('layouts.institute')
@section('title', $student->profile?->name ?? $student->user_id)
@section('page-title', $student->profile?->name ?? $student->user_id)
@section('topbar-actions')
  <a href="{{ route('institute.students.index') }}" class="btn btn-outline btn-sm">← Back to List</a>
  <a href="{{ route('institute.students.ledger', $student) }}" class="btn btn-outline btn-sm">Ledger</a>
@endsection

@push('styles')
<style>
/* ── Student Banner ── */
.sb-banner {
  background: var(--bg-2);
  border: 1px solid var(--border);
  border-radius: 18px;
  padding: 28px 32px;
  margin-bottom: 22px;
  display: flex;
  gap: 24px;
  align-items: flex-start;
}
.sb-avatar {
  width: 90px; height: 90px;
  border-radius: 50%;
  flex-shrink: 0;
  background: var(--accent);
  color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: 34px; font-weight: 900;
  overflow: hidden;
  box-shadow: 0 0 0 5px rgba(108,93,211,.15);
}
.sb-avatar img { width:100%; height:100%; object-fit:cover; }
.sb-info { flex: 1; min-width: 0; }
.sb-name { font-size: 24px; font-weight: 900; line-height: 1.2; margin-bottom: 6px; }
.sb-contacts { display: flex; flex-wrap: wrap; gap: 6px 20px; font-size: 13px; color: var(--text-2); margin-bottom: 10px; }
.sb-contacts span { display: flex; align-items: center; gap: 5px; }
.sb-basics { display: flex; flex-wrap: wrap; gap: 6px; }
.sb-chip {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 12px; border-radius: 8px;
  background: var(--bg-3); border: 1px solid var(--border);
  font-size: 12px; font-weight: 600; color: var(--text-2);
}
.sb-chip-label { font-size: 10px; text-transform: uppercase; letter-spacing: .05em; color: var(--text-3); margin-right: 2px; }
.sb-actions { display: flex; flex-direction: column; gap: 8px; align-items: flex-end; flex-shrink: 0; }

/* ── Enrollment Table ── */
.enr-section { background: var(--bg-2); border: 1px solid var(--border); border-radius: 18px; overflow: hidden; }
.enr-section-head {
  padding: 16px 22px;
  border-bottom: 1px solid var(--border);
  display: flex; justify-content: space-between; align-items: center;
}
.enr-section-title { font-size: 14px; font-weight: 800; }

/* ── Enrollment Card ── */
.enr-card {
  padding: 20px 22px;
  border-bottom: 1px solid var(--border);
}
.enr-card:last-child { border-bottom: none; }

.enr-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
.enr-course { font-size: 16px; font-weight: 800; }
.enr-meta { font-size: 12px; color: var(--text-2); margin-top: 3px; display: flex; flex-wrap: wrap; gap: 4px 12px; }

.enr-dates {
  display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px;
}
.enr-date-chip {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 12px; border-radius: 8px;
  background: var(--bg-3); border: 1px solid var(--border);
  font-size: 12px;
}
.enr-date-chip .lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing:.05em; color: var(--text-3); }
.enr-date-chip .val { font-weight: 700; color: var(--text); }

.enr-fee-row {
  display: grid; grid-template-columns: repeat(3, 1fr) auto;
  gap: 10px; align-items: center;
}
@media(max-width:640px) { .enr-fee-row { grid-template-columns: repeat(3,1fr); } .enr-fee-row .enr-btn-col { grid-column: 1/-1; } }

.enr-fee-box {
  background: var(--bg-3); border: 1px solid var(--border);
  border-radius: 10px; padding: 10px 14px;
}
.enr-fee-lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--text-3); margin-bottom: 3px; }
.enr-fee-val { font-size: 16px; font-weight: 900; }
.enr-fee-box.due-box { background: #fef2f2; border-color: #fecaca; }
.enr-fee-box.due-box .enr-fee-lbl { color: #b91c1c; }
.enr-fee-box.due-box .enr-fee-val { color: #dc2626; }
.enr-fee-box.paid-box { background: #f0fdf4; border-color: #bbf7d0; }
.enr-fee-box.paid-box .enr-fee-lbl { color: #15803d; }
.enr-fee-box.paid-box .enr-fee-val { color: #16a34a; }
.enr-fee-box.clear-box .enr-fee-val { color: #16a34a; }

.enr-btn-col { display: flex; flex-direction: column; gap: 6px; }

/* expired notice */
.enr-expired-notice {
  margin-bottom: 12px; padding: 10px 14px;
  background: #fef2f2; border: 1px solid #fecaca;
  border-radius: 10px; font-size: 12px; color: #b91c1c;
  display: flex; align-items: center; gap: 8px;
}
</style>
@endpush

@section('content')
@php
  $profile  = $student->profile;
  $wallet   = $student->studentWallet;
  $bal      = $wallet?->balance ?? 0;
  $photo    = $profile?->photo;
  $hasPhoto = $photo && $photo !== 'images/user.svg';
  $initials = strtoupper(substr($profile?->name ?? 'S', 0, 1));
@endphp

{{-- ══ Student Banner ══ --}}
<div class="sb-banner">
  <div class="sb-avatar">
    @if($hasPhoto)<img src="{{ asset($photo) }}" alt="">@else{{ $initials }}@endif
  </div>

  <div class="sb-info">
    <div class="sb-name">{{ $profile?->name ?? $student->user_id }}</div>

    <div class="sb-contacts">
      <span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/></svg>
        {{ $student->mobile }}
      </span>
      @if($student->email)
      <span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        {{ $student->email }}
      </span>
      @endif
      <span style="font-family:monospace;font-size:12px;color:var(--text-3)">
        ID: {{ $student->user_id }}
      </span>
    </div>

    <div class="sb-basics">
      <span class="sb-chip" style="{{ $student->status==='active' ? 'background:#f0fdf4;border-color:#bbf7d0;color:#15803d' : 'background:#fef2f2;border-color:#fecaca;color:#b91c1c' }}">
        {{ $student->status === 'active' ? '● Active' : '● Inactive' }}
      </span>
      @if($profile?->father_name)
        <span class="sb-chip"><span class="sb-chip-label">Father</span> {{ $profile->father_name }}</span>
      @endif
      @if($profile?->dob)
        <span class="sb-chip"><span class="sb-chip-label">DOB</span> {{ $profile->dob->format('d M Y') }}</span>
      @endif
      @if($profile?->gender)
        <span class="sb-chip"><span class="sb-chip-label">Gender</span> {{ $profile->gender }}</span>
      @endif
      @if($profile?->blood_group)
        <span class="sb-chip"><span class="sb-chip-label">Blood</span> {{ $profile->blood_group }}</span>
      @endif
      @if($profile?->qualification)
        <span class="sb-chip"><span class="sb-chip-label">Edu.</span> {{ $profile->qualification }}</span>
      @endif
      @if($profile?->city)
        <span class="sb-chip">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          {{ $profile->city }}{{ $profile?->state ? ', '.$profile->state : '' }}
        </span>
      @endif
      {{-- Wallet balance chip --}}
      <span class="sb-chip" style="{{ $bal < 0 ? 'background:#fef2f2;border-color:#fecaca;color:#b91c1c' : ($bal > 0 ? 'background:#f0fdf4;border-color:#bbf7d0;color:#15803d' : '') }}">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <span class="sb-chip-label">Wallet</span>
        @if($bal < 0) ₹{{ number_format(abs($bal),2) }} due
        @elseif($bal > 0) ₹{{ number_format($bal,2) }} credit
        @else Clear @endif
      </span>
    </div>
  </div>

  <div class="sb-actions">
    <a href="{{ route('institute.students.edit', $student) }}" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap;">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Edit Profile
    </a>
    <a href="{{ route('institute.students.ledger', $student) }}" class="btn btn-outline btn-sm" style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap;">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Ledger
    </a>
  </div>
</div>

{{-- ══ Enrollments ══ --}}
<div class="enr-section">
  <div class="enr-section-head">
    <div class="enr-section-title">Course Enrollments</div>
    <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-outline btn-xs">+ New Enrollment</a>
  </div>

  @forelse($enrollments as $e)
    @php
      $amtCol   = \App\Models\FeeCollectDetail::amountColumn();
      $ePaid    = (float)\App\Models\FeeCollectDetail::where('course_book_id',$e->id)->whereNull('cancelled_at')->sum($amtCol);
      $eDue     = max((float)$e->final_fee - $ePaid, 0);
      $duration = (int)($e->course?->duration ?? 0);
      $startDate = $e->start_date;
      $expectedEnd = $startDate && $duration ? $startDate->copy()->addMonths($duration) : null;
      $statusMap = ['RUN'=>'badge-success','OPEN'=>'badge-warning','CLOSE'=>'badge-neutral','EXPIRED'=>'badge-danger'];
      $statusLabel = ['RUN'=>'Admitted','OPEN'=>'Seat Booked','CLOSE'=>'Closed','EXPIRED'=>'Expired'];
    @endphp
    <div class="enr-card">

      {{-- Top row: course + status --}}
      <div class="enr-top">
        <div>
          <div class="enr-course">{{ $e->course?->name }}</div>
          <div class="enr-meta">
            @if($e->batch?->name)<span>{{ $e->batch->name }}</span>@endif
            @if($e->enrollment_no)<span style="font-family:monospace;font-size:11px;">{{ $e->enrollment_no }}</span>@endif
            @if($e->paymentPlan?->plan_type)<span style="font-weight:700;color:var(--accent)">{{ $e->paymentPlan->plan_type }}</span>@endif
          </div>
        </div>
        <span class="badge {{ $statusMap[$e->status] ?? 'badge-neutral' }}" style="flex-shrink:0;font-size:12px;">
          {{ $statusLabel[$e->status] ?? $e->status }}
        </span>
      </div>

      {{-- Date chips --}}
      @if($startDate || $expectedEnd || $e->book_date)
      <div class="enr-dates">
        @if($e->book_date)
          <div class="enr-date-chip">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <div><div class="lbl">Booked On</div><div class="val">{{ \Carbon\Carbon::parse($e->book_date)->format('d M Y') }}</div></div>
          </div>
        @endif
        @if($startDate)
          <div class="enr-date-chip" style="border-color:#a5b4fc;background:#eef2ff">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <div><div class="lbl" style="color:#6366f1">Started</div><div class="val">{{ $startDate->format('d M Y') }}</div></div>
          </div>
        @endif
        @if($expectedEnd)
          <div class="enr-date-chip" style="border-color:#fed7aa;background:#fff7ed">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <div><div class="lbl" style="color:#ea580c">Expected End</div><div class="val">{{ $expectedEnd->format('d M Y') }}</div></div>
          </div>
        @endif
        @if($duration)
          <div class="enr-date-chip">
            <div><div class="lbl">Duration</div><div class="val">{{ $duration }} month{{ $duration > 1 ? 's' : '' }}</div></div>
          </div>
        @endif
      </div>
      @endif

      @if($e->status === 'EXPIRED')
        <div class="enr-expired-notice">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          Seat booking expired. Renew to continue with admission.
        </div>
      @endif

      {{-- Fee stats + action --}}
      <div class="enr-fee-row">
        <div class="enr-fee-box">
          <div class="enr-fee-lbl">Total Fee</div>
          <div class="enr-fee-val">₹{{ number_format($e->final_fee, 2) }}</div>
        </div>
        <div class="enr-fee-box paid-box">
          <div class="enr-fee-lbl">Paid</div>
          <div class="enr-fee-val">₹{{ number_format($ePaid, 2) }}</div>
        </div>
        <div class="enr-fee-box {{ $eDue > 0 ? 'due-box' : 'clear-box' }}">
          <div class="enr-fee-lbl">Due</div>
          <div class="enr-fee-val">{{ $eDue > 0 ? '₹'.number_format($eDue,2) : '✓ Clear' }}</div>
        </div>

        <div class="enr-btn-col">
          @if($e->status === 'EXPIRED')
            <form method="POST" action="{{ route('institute.enrollment.renew', $e) }}" class="renew-form" style="margin:0">
              @csrf<button type="button" class="btn btn-primary btn-sm renew-btn" style="white-space:nowrap">↺ Renew</button>
            </form>
            <form method="POST" action="{{ route('institute.enrollment.cancel', $e) }}" class="cancel-form" style="margin:0">
              @csrf<button type="button" class="btn btn-outline btn-sm cancel-btn" style="color:#dc2626;border-color:#fca5a5;white-space:nowrap">Cancel</button>
            </form>

          @elseif($e->status === 'OPEN')
            <a href="{{ route('institute.enrollment.fee', $e) }}" class="btn btn-primary btn-sm" style="white-space:nowrap;display:inline-flex;align-items:center;gap:5px">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              Collect Fee
            </a>
            <a href="{{ route('institute.enrollment.profile', $e) }}" class="btn btn-outline btn-sm" style="white-space:nowrap">Fill Details</a>
            <form method="POST" action="{{ route('institute.enrollment.cancel', $e) }}" class="cancel-form" style="margin:0">
              @csrf<button type="button" class="btn btn-outline btn-sm cancel-btn" style="color:#dc2626;border-color:#fca5a5;white-space:nowrap">Cancel</button>
            </form>

          @else
            <a href="{{ route('institute.enrollment.payment-complete', $e) }}" class="btn btn-primary btn-sm" style="white-space:nowrap;display:inline-flex;align-items:center;gap:5px">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              Collect Fee
            </a>
            <a href="{{ route('institute.enrollment.preview', $e) }}" class="btn btn-outline btn-sm" style="white-space:nowrap">Application Form</a>
            <a href="{{ route('institute.students.enrollments.edit', [$student, $e]) }}" class="btn btn-outline btn-sm" style="white-space:nowrap">Change Course</a>
          @endif
        </div>
      </div>

    </div>
  @empty
    <div style="padding:40px;text-align:center;color:var(--text-3);font-size:13px;">
      No enrollments yet.
      <a href="{{ route('institute.enrollment.choose') }}" style="color:var(--accent)">Enroll now →</a>
    </div>
  @endforelse
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.cancel-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const form = this.closest('.cancel-form');
    Swal.fire({
      title: 'Cancel Booking?',
      html: 'This booking will be marked as <b>Cancelled</b>.',
      icon: 'warning', showCancelButton: true,
      confirmButtonText: 'Yes, Cancel', cancelButtonText: 'No',
      confirmButtonColor: '#dc2626', reverseButtons: true,
    }).then(r => { if (r.isConfirmed) { btn.disabled = true; form.submit(); } });
  });
});
document.querySelectorAll('.renew-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const form = this.closest('.renew-form');
    Swal.fire({
      title: 'Renew Booking?', html: 'Booking date will reset to <b>today</b>.',
      icon: 'question', showCancelButton: true,
      confirmButtonText: 'Yes, Renew', cancelButtonText: 'Cancel',
      confirmButtonColor: 'var(--accent)', reverseButtons: true,
    }).then(r => { if (r.isConfirmed) { btn.disabled = true; form.submit(); } });
  });
});
</script>
@endpush
