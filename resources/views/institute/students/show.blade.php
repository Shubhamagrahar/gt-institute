@extends('layouts.institute')
@section('title', $student->profile?->name ?? $student->user_id)
@section('page-title', $student->profile?->name ?? $student->user_id)
@section('topbar-actions')
  <a href="{{ route('institute.students.index') }}" class="btn btn-outline btn-sm">← Back to List</a>
  <a href="{{ route('institute.students.ledger', $student) }}" class="btn btn-outline btn-sm">Ledger</a>
@endsection

@push('styles')
<style>
/* ── Hero ── */
.sp-hero {
  background: linear-gradient(135deg, var(--bg-2), var(--bg-3));
  border: 1px solid var(--border);
  border-radius: 18px;
  padding: 24px 28px;
  display: flex;
  align-items: center;
  gap: 22px;
  margin-bottom: 20px;
}
.sp-avatar {
  width: 80px; height: 80px; border-radius: 50%; flex-shrink: 0;
  background: var(--accent); color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: 30px; font-weight: 900; overflow: hidden;
  box-shadow: 0 0 0 4px rgba(108,93,211,.18);
}
.sp-avatar img { width: 100%; height: 100%; object-fit: cover; }
.sp-name { font-size: 22px; font-weight: 900; line-height: 1.2; color: var(--text); }
.sp-meta { font-size: 13px; color: var(--text-2); margin-top: 5px; display: flex; flex-wrap: wrap; gap: 6px 16px; }
.sp-meta span { display: flex; align-items: center; gap: 5px; }
.sp-badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; letter-spacing: .04em; margin-top: 8px; }
.sp-badge-active { background: #dcfce7; color: #15803d; }
.sp-badge-inactive { background: #fef2f2; color: #b91c1c; }

/* ── Layout ── */
.sp-body { display: grid; grid-template-columns: 1fr 360px; gap: 20px; align-items: start; }
@media(max-width:900px) { .sp-body { grid-template-columns: 1fr; } }

/* ── Section card ── */
.sp-section { background: var(--bg-2); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; margin-bottom: 16px; }
.sp-section:last-child { margin-bottom: 0; }
.sp-section-head { padding: 13px 18px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; }
.sp-section-title { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: var(--text-2); }
.sp-fields { display: grid; grid-template-columns: 1fr 1fr; }
.sp-field { padding: 10px 16px; border-bottom: 1px solid var(--border); font-size: 13px; }
.sp-field:nth-child(odd) { border-right: 1px solid var(--border); }
.sp-field:last-child, .sp-field:nth-last-child(2):nth-child(odd) { border-bottom: none; }
.sp-field-lbl { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--text-3); margin-bottom: 2px; }
.sp-field-val { font-weight: 600; color: var(--text); }
.sp-field-full { grid-column: 1 / -1; border-right: none !important; }

/* ── Wallet ── */
.sp-wallet { background: var(--bg-2); border: 1px solid var(--border); border-radius: 16px; padding: 16px 18px; margin-bottom: 16px; }
.sp-wallet-amt { font-size: 26px; font-weight: 900; }

/* ── Enrollment card ── */
.sp-enroll { background: var(--bg-2); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; margin-bottom: 14px; }
.sp-enroll:last-child { margin-bottom: 0; }
.sp-enroll-head { padding: 14px 16px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
.sp-course-name { font-size: 15px; font-weight: 800; }
.sp-course-meta { font-size: 12px; color: var(--text-2); margin-top: 3px; }
.sp-fee-stats { display: grid; grid-template-columns: repeat(3, 1fr); border-bottom: 1px solid var(--border); }
.sp-stat { padding: 11px 14px; text-align: center; }
.sp-stat:not(:last-child) { border-right: 1px solid var(--border); }
.sp-stat-lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--text-3); margin-bottom: 3px; }
.sp-stat-val { font-size: 14px; font-weight: 900; }
.sp-actions { padding: 12px 14px; display: flex; gap: 8px; flex-wrap: wrap; }
.sp-new-enroll { background: var(--bg-2); border: 1px solid var(--border); border-radius: 16px; padding: 16px 18px; display: flex; justify-content: space-between; align-items: center; }
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
  $isActive = $student->status === 'active';
@endphp

{{-- ── Hero ── --}}
<div class="sp-hero">
  <div class="sp-avatar">
    @if($hasPhoto)<img src="{{ asset($photo) }}" alt="">@else{{ $initials }}@endif
  </div>
  <div style="flex:1;min-width:0">
    <div class="sp-name">{{ $profile?->name ?? $student->user_id }}</div>
    <div class="sp-meta">
      <span>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
        {{ $student->mobile }}
      </span>
      @if($student->email)
      <span>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        {{ $student->email }}
      </span>
      @endif
      <span style="color:var(--text-3)">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        {{ $student->user_id }}
      </span>
    </div>
    <div>
      <span class="sp-badge {{ $isActive ? 'sp-badge-active' : 'sp-badge-inactive' }}">
        {{ $isActive ? '● Active' : '● Inactive' }}
      </span>
    </div>
  </div>
  <a href="{{ route('institute.students.edit', $student) }}" class="btn btn-primary btn-sm" style="flex-shrink:0;display:inline-flex;align-items:center;gap:6px;">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
    Edit Profile
  </a>
</div>

{{-- ── Body ── --}}
<div class="sp-body">

  {{-- Left: Profile Details --}}
  <div>
    @php
      $personalFields = [
        'Father\'s Name'  => $profile?->father_name,
        'Mother\'s Name'  => $profile?->mother_name,
        'Date of Birth'   => $profile?->dob?->format('d M Y'),
        'Gender'          => $profile?->gender,
        'Category'        => $profile?->category,
        'Blood Group'     => $profile?->blood_group,
        'Religion'        => $profile?->religion,
        'Nationality'     => $profile?->nationality,
      ];
      $contactFields = [
        'WhatsApp'        => $profile?->whatsapp_no,
        'Alt. Mobile'     => $profile?->alternate_mobile,
        'Guardian'        => $profile?->guardian_name . ($profile?->guardian_mobile ? ' · '.$profile->guardian_mobile : ''),
        'Guardian Rel.'   => $profile?->guardian_relation,
        'Aadhar No.'      => $profile?->aadhar_no,
        'PAN No.'         => $profile?->pan_no,
        'Qualification'   => $profile?->qualification,
      ];
      $addressFields = [
        'State'    => $profile?->state,
        'District' => $profile?->district,
        'City'     => $profile?->city,
        'PIN Code' => $profile?->pin_code,
      ];
      $personalFilled = collect($personalFields)->filter()->count();
      $contactFilled  = collect($contactFields)->filter()->count();
      $addressFilled  = collect($addressFields)->filter()->count() + ($profile?->address ? 1 : 0) + ($profile?->permanent_address ? 1 : 0);
    @endphp

    @if($personalFilled > 0)
    <div class="sp-section">
      <div class="sp-section-head">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span class="sp-section-title">Personal Information</span>
      </div>
      <div class="sp-fields">
        @foreach($personalFields as $lbl => $val)
          @if($val)
          <div class="sp-field">
            <div class="sp-field-lbl">{{ $lbl }}</div>
            <div class="sp-field-val">{{ $val }}</div>
          </div>
          @endif
        @endforeach
      </div>
    </div>
    @endif

    @if($contactFilled > 0)
    <div class="sp-section">
      <div class="sp-section-head">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <span class="sp-section-title">Contact & Documents</span>
      </div>
      <div class="sp-fields">
        @foreach($contactFields as $lbl => $val)
          @if($val)
          <div class="sp-field">
            <div class="sp-field-lbl">{{ $lbl }}</div>
            <div class="sp-field-val">{{ $val }}</div>
          </div>
          @endif
        @endforeach
      </div>
    </div>
    @endif

    @if($addressFilled > 0)
    <div class="sp-section">
      <div class="sp-section-head">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <span class="sp-section-title">Address</span>
      </div>
      <div class="sp-fields">
        @foreach($addressFields as $lbl => $val)
          @if($val)
          <div class="sp-field">
            <div class="sp-field-lbl">{{ $lbl }}</div>
            <div class="sp-field-val">{{ $val }}</div>
          </div>
          @endif
        @endforeach
        @if($profile?->address)
          <div class="sp-field sp-field-full">
            <div class="sp-field-lbl">Present Address</div>
            <div class="sp-field-val">{{ $profile->address }}</div>
          </div>
        @endif
        @if($profile?->permanent_address)
          <div class="sp-field sp-field-full" style="border-bottom:none">
            <div class="sp-field-lbl">Permanent Address</div>
            <div class="sp-field-val">{{ $profile->permanent_address }}</div>
          </div>
        @endif
      </div>
    </div>
    @endif

    @if($personalFilled === 0 && $contactFilled === 0 && $addressFilled === 0)
    <div class="sp-section">
      <div style="padding:32px;text-align:center;color:var(--text-3);font-size:13px;">
        No profile details filled yet.
        <a href="{{ route('institute.students.edit', $student) }}" style="color:var(--accent)">Add details →</a>
      </div>
    </div>
    @endif
  </div>

  {{-- Right: Wallet + Enrollments --}}
  <div>

    {{-- Wallet --}}
    <div class="sp-wallet">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
        <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-3);">Wallet Balance</div>
        @if($bal < 0)
          <span style="font-size:11px;background:#fef2f2;color:#b91c1c;padding:2px 8px;border-radius:6px;font-weight:700;">Amount Due</span>
        @elseif($bal > 0)
          <span style="font-size:11px;background:#f0fdf4;color:#15803d;padding:2px 8px;border-radius:6px;font-weight:700;">Credit</span>
        @endif
      </div>
      <div class="sp-wallet-amt {{ $bal < 0 ? 'amount-neg' : ($bal > 0 ? 'amount-pos' : '') }}">
        ₹{{ number_format(abs($bal), 2) }}
      </div>
      <div style="font-size:12px;color:var(--text-3);margin-top:4px;">
        {{ $bal < 0 ? 'Student owes this amount.' : ($bal > 0 ? 'Advance / credit available.' : 'No outstanding balance.') }}
      </div>
    </div>

    {{-- Enrollments --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
      <div style="font-size:13px;font-weight:700;color:var(--text);">Enrollments</div>
      <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-outline btn-xs">+ New</a>
    </div>

    @forelse($enrollments as $e)
      @php
        $amtCol = \App\Models\FeeCollectDetail::amountColumn();
        $ePaid  = (float)\App\Models\FeeCollectDetail::where('course_book_id',$e->id)->whereNull('cancelled_at')->sum($amtCol);
        $eDue   = max($e->final_fee - $ePaid, 0);
        $statusMap = ['RUN' => 'badge-success', 'OPEN' => 'badge-warning', 'CLOSE' => 'badge-neutral', 'EXPIRED' => 'badge-danger'];
        $statusLabel = ['RUN' => 'Admitted', 'OPEN' => 'Seat Booked', 'CLOSE' => 'Closed', 'EXPIRED' => 'Expired'];
      @endphp
      <div class="sp-enroll">
        {{-- Header --}}
        <div class="sp-enroll-head">
          <div>
            <div class="sp-course-name">{{ $e->course?->name }}</div>
            <div class="sp-course-meta">
              {{ $e->batch?->name ?? 'No Batch' }}
              @if($e->enrollment_no) &nbsp;·&nbsp; <span style="font-family:monospace;font-size:11px;">{{ $e->enrollment_no }}</span> @endif
            </div>
          </div>
          <span class="badge {{ $statusMap[$e->status] ?? 'badge-neutral' }}" style="flex-shrink:0;">
            {{ $statusLabel[$e->status] ?? $e->status }}
          </span>
        </div>

        {{-- Fee stats (always show) --}}
        <div class="sp-fee-stats">
          <div class="sp-stat">
            <div class="sp-stat-lbl">Total</div>
            <div class="sp-stat-val">₹{{ number_format($e->final_fee, 2) }}</div>
          </div>
          <div class="sp-stat">
            <div class="sp-stat-lbl" style="color:#15803d">Paid</div>
            <div class="sp-stat-val" style="color:#16a34a">₹{{ number_format($ePaid, 2) }}</div>
          </div>
          <div class="sp-stat" style="background:{{ $eDue > 0 ? '#fef2f2' : 'transparent' }}">
            <div class="sp-stat-lbl" style="color:{{ $eDue > 0 ? '#b91c1c' : 'var(--text-3)' }}">Due</div>
            <div class="sp-stat-val" style="color:{{ $eDue > 0 ? '#dc2626' : 'var(--text-2)' }}">
              @if($eDue > 0) ₹{{ number_format($eDue, 2) }} @else ✓ Clear @endif
            </div>
          </div>
        </div>

        @if($e->status === 'EXPIRED')
          <div style="margin:10px 14px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;font-size:12px;color:#b91c1c;">
            Seat booking expired. Renew to continue with admission.
          </div>
        @endif

        {{-- Actions --}}
        <div class="sp-actions">
          @if($e->status === 'EXPIRED')
            <form method="POST" action="{{ route('institute.enrollment.renew', $e) }}" class="renew-form" style="margin:0;">
              @csrf
              <button type="button" class="btn btn-primary btn-xs renew-btn">↺ Renew Booking</button>
            </form>
            <form method="POST" action="{{ route('institute.enrollment.cancel', $e) }}" class="cancel-form" style="margin:0;">
              @csrf
              <button type="button" class="btn btn-outline btn-xs cancel-btn" style="color:#dc2626;border-color:#fca5a5;">Cancel</button>
            </form>

          @elseif($e->status === 'OPEN')
            <a href="{{ route('institute.enrollment.profile', $e) }}" class="btn btn-outline btn-xs">Fill Details</a>
            <a href="{{ route('institute.enrollment.fee', $e) }}" class="btn btn-primary btn-xs">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:3px"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              Collect Fee
            </a>
            <form method="POST" action="{{ route('institute.enrollment.cancel', $e) }}" class="cancel-form" style="margin:0;">
              @csrf
              <button type="button" class="btn btn-outline btn-xs cancel-btn" style="color:#dc2626;border-color:#fca5a5;">Cancel</button>
            </form>

          @else
            <a href="{{ route('institute.enrollment.payment-complete', $e) }}" class="btn btn-primary btn-xs" style="display:inline-flex;align-items:center;gap:5px;">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              Collect Fee
            </a>
            <a href="{{ route('institute.enrollment.preview', $e) }}" class="btn btn-outline btn-xs">Application Form</a>
            <a href="{{ route('institute.students.enrollments.edit', [$student, $e]) }}" class="btn btn-outline btn-xs">Change Course</a>
          @endif
        </div>
      </div>
    @empty
      <div class="sp-enroll" style="padding:28px;text-align:center;color:var(--text-3);font-size:13px;">
        No enrollments yet.
        <a href="{{ route('institute.enrollment.choose') }}" style="color:var(--accent)">Enroll now →</a>
      </div>
    @endforelse

  </div>
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
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, Cancel',
      cancelButtonText: 'No',
      confirmButtonColor: '#dc2626',
      reverseButtons: true,
    }).then(result => {
      if (result.isConfirmed) { btn.disabled = true; form.submit(); }
    });
  });
});

document.querySelectorAll('.renew-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const form = this.closest('.renew-form');
    Swal.fire({
      title: 'Renew Booking?',
      html: 'The booking date will be reset to <b>today</b>.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, Renew',
      cancelButtonText: 'Cancel',
      confirmButtonColor: 'var(--accent)',
      reverseButtons: true,
    }).then(result => {
      if (result.isConfirmed) { btn.disabled = true; form.submit(); }
    });
  });
});
</script>
@endpush
