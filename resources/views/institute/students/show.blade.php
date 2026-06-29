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

.enr-tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
.enr-tbl th {
  padding: 10px 14px; text-align: left;
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .07em; color: var(--text-2);
  background: var(--bg-3); border-bottom: 1px solid var(--border);
  white-space: nowrap;
}
.enr-tbl td { padding: 13px 14px; border-bottom: 1px solid var(--border); vertical-align: middle; }
.enr-tbl tbody tr:last-child td { border-bottom: none; }
.enr-tbl tbody tr:hover td { background: var(--bg-3); }
.enr-row-muted td { opacity: .65; }
.text-muted { color: var(--text-3); }
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

{{-- ══ Enrollments Table ══ --}}
<div class="enr-section">
  <div class="enr-section-head">
    <div class="enr-section-title">Course Enrollments</div>
    <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-outline btn-xs">+ New Enrollment</a>
  </div>

  @if($enrollments->isEmpty())
    <div style="padding:40px;text-align:center;color:var(--text-3);font-size:13px;">
      No enrollments yet.
      <a href="{{ route('institute.enrollment.choose') }}" style="color:var(--accent)">Enroll now →</a>
    </div>
  @else
  <div style="overflow-x:auto">
    <table class="enr-tbl">
      <thead>
        <tr>
          <th>#</th>
          <th>Course</th>
          <th>Enrollment No.</th>
          <th>Status</th>
          <th>Start Date</th>
          <th>Expected End</th>
          <th style="text-align:right">Total Fee</th>
          <th style="text-align:right">Paid</th>
          <th style="text-align:right">Due</th>
          <th style="text-align:right">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($enrollments as $i => $e)
          @php
            $amtCol      = \App\Models\FeeCollectDetail::amountColumn();
            $ePaid       = (float)\App\Models\FeeCollectDetail::where('course_book_id',$e->id)->whereNull('cancelled_at')->sum($amtCol);
            $eDue        = max((float)$e->final_fee - $ePaid, 0);
            $duration    = (int)($e->course?->duration ?? 0);
            $startDate   = $e->start_date;
            $expectedEnd = $startDate && $duration ? $startDate->copy()->addMonths($duration) : null;
            $statusColors = [
              'RUN'     => ['bg'=>'#f0fdf4','color'=>'#15803d','dot'=>'#16a34a'],
              'OPEN'    => ['bg'=>'#fffbeb','color'=>'#b45309','dot'=>'#d97706'],
              'CLOSE'   => ['bg'=>'var(--bg-3)','color'=>'var(--text-2)','dot'=>'#9ca3af'],
              'EXPIRED' => ['bg'=>'#fef2f2','color'=>'#b91c1c','dot'=>'#dc2626'],
            ];
            $sc = $statusColors[$e->status] ?? $statusColors['CLOSE'];
            $statusLabel = ['RUN'=>'Running','OPEN'=>'Seat Booked','CLOSE'=>'Closed','EXPIRED'=>'Expired'];
          @endphp
          <tr class="{{ in_array($e->status,['CLOSE','EXPIRED']) ? 'enr-row-muted' : '' }}">
            <td class="text-muted">{{ $i + 1 }}</td>
            <td>
              <div style="font-weight:700;font-size:13px;">{{ $e->course?->name }}</div>
              @if($e->batch?->name)
                <div style="font-size:11px;color:var(--text-3);margin-top:1px;">{{ $e->batch->name }}</div>
              @endif
              @if($e->paymentPlan?->plan_type)
                <div style="font-size:11px;color:var(--accent);font-weight:700;margin-top:1px;">{{ $e->paymentPlan->plan_type }}</div>
              @endif
            </td>
            <td>
              @if($e->enrollment_no)
                <span style="font-family:monospace;font-size:12px;font-weight:700;">{{ $e->enrollment_no }}</span>
              @else
                <span class="text-muted" style="font-size:12px">—</span>
              @endif
            </td>
            <td>
              <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:700;background:{{ $sc['bg'] }};color:{{ $sc['color'] }}">
                <span style="width:6px;height:6px;border-radius:50%;background:{{ $sc['dot'] }};flex-shrink:0"></span>
                {{ $statusLabel[$e->status] ?? $e->status }}
              </span>
            </td>
            <td style="font-size:12px;white-space:nowrap;color:var(--text-2)">
              {{ $startDate ? $startDate->format('d M Y') : '—' }}
            </td>
            <td style="font-size:12px;white-space:nowrap;color:var(--text-2)">
              {{ $expectedEnd ? $expectedEnd->format('d M Y') : '—' }}
              @if($duration)<div style="font-size:10px;color:var(--text-3)">{{ $duration }}m course</div>@endif
            </td>
            <td style="text-align:right;font-weight:700;font-size:13px;white-space:nowrap">
              ₹{{ number_format($e->final_fee, 2) }}
            </td>
            <td style="text-align:right;font-weight:700;font-size:13px;white-space:nowrap;color:#16a34a">
              ₹{{ number_format($ePaid, 2) }}
            </td>
            <td style="text-align:right;font-weight:700;font-size:13px;white-space:nowrap;{{ $eDue > 0 ? 'color:#dc2626' : 'color:#16a34a' }}">
              {{ $eDue > 0 ? '₹'.number_format($eDue,2) : '✓ Clear' }}
            </td>
            <td style="text-align:right;white-space:nowrap">
              @if($e->status === 'EXPIRED')
                <form method="POST" action="{{ route('institute.enrollment.renew', $e) }}" class="renew-form" style="display:inline;margin:0">
                  @csrf<button type="button" class="btn btn-primary btn-xs renew-btn">↺ Renew</button>
                </form>
                <form method="POST" action="{{ route('institute.enrollment.cancel', $e) }}" class="cancel-form" style="display:inline;margin:0">
                  @csrf<button type="button" class="btn btn-outline btn-xs cancel-btn" style="color:#dc2626;border-color:#fca5a5">Cancel</button>
                </form>

              @elseif($e->status === 'OPEN')
                <a href="{{ route('institute.enrollment.fee', $e) }}" class="btn btn-primary btn-xs">Collect Fee</a>
                <a href="{{ route('institute.enrollment.profile', $e) }}" class="btn btn-outline btn-xs">Details</a>
                <form method="POST" action="{{ route('institute.enrollment.cancel', $e) }}" class="cancel-form" style="display:inline;margin:0">
                  @csrf<button type="button" class="btn btn-outline btn-xs cancel-btn" style="color:#dc2626;border-color:#fca5a5">Cancel</button>
                </form>

              @elseif($e->status === 'RUN')
                <a href="{{ route('institute.enrollment.payment-complete', $e) }}" class="btn btn-primary btn-xs">Collect Fee</a>
                <a href="{{ route('institute.enrollment.preview', $e) }}" class="btn btn-outline btn-xs">Form</a>

              @else
                <a href="{{ route('institute.enrollment.payment-complete', $e) }}" class="btn btn-outline btn-xs">View</a>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
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
