@extends('layouts.institute')
@section('title', $student->profile?->name ?? $student->user_id)
@section('page-title', $student->profile?->name ?? $student->user_id)
@section('topbar-actions')
  <a href="{{ route('institute.students.index') }}" class="btn btn-outline btn-sm">← Back to List</a>
  <a href="{{ route('institute.students.ledger', $student) }}" class="btn btn-outline btn-sm">Ledger</a>
@endsection

@push('styles')
<style>
.profile-hero {
  display: flex; gap: 20px; align-items: flex-start;
  padding: 20px; border-bottom: 1px solid var(--border);
}
.profile-photo {
  width: 80px; height: 80px; border-radius: 50%; object-fit: cover;
  border: 3px solid var(--border); flex-shrink: 0; overflow: hidden;
  background: var(--accent); display: flex; align-items: center;
  justify-content: center; font-size: 28px; font-weight: 900; color: #fff;
}
.profile-photo img { width:100%; height:100%; object-fit:cover; }
.info-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
.info-cell { padding: 9px 14px; border-bottom: 1px solid var(--border); font-size: 13px; }
.info-cell:nth-child(odd) { border-right: 1px solid var(--border); }
.info-lbl { font-size: 10.5px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .06em; color: var(--text-2); margin-bottom: 2px; }
.info-val { font-weight: 600; }
.enroll-card {
  border: 1px solid var(--border); border-radius: 14px;
  padding: 14px 16px; margin-bottom: 12px;
}
.enroll-card:last-child { margin-bottom: 0; }
.enroll-actions {
  display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px;
  padding-top: 12px; border-top: 1px solid var(--border);
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
  $runEnr   = $enrollments->firstWhere('status','RUN');
@endphp

<div style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start;">

  {{-- ── Left: Profile ── --}}
  <div>
    <div class="gt-card" style="overflow:hidden;padding:0;">

      {{-- Hero --}}
      <div class="profile-hero">
        <div class="profile-photo">
          @if($hasPhoto)
            <img src="{{ asset($photo) }}" alt="">
          @else
            {{ strtoupper(substr($profile?->name ?? 'S', 0, 1)) }}
          @endif
        </div>
        <div style="flex:1">
          <div style="font-size:20px;font-weight:900;line-height:1.2">
            {{ $profile?->name ?? $student->user_id }}
          </div>
          <div style="font-size:12px;color:var(--text-2);margin-top:3px;">
            <code style="color:var(--accent)">{{ $student->user_id }}</code>
            &nbsp;·&nbsp; {{ $student->mobile }}
            @if($student->email) &nbsp;·&nbsp; {{ $student->email }} @endif
          </div>
          <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;">
            <span class="badge {{ $student->status==='active'?'badge-success':'badge-danger' }}">
              {{ ucfirst($student->status) }}
            </span>
          </div>
        </div>
        {{-- Edit profile button --}}
        <a href="{{ route('institute.students.edit', $student) }}"
           class="btn btn-primary btn-sm" style="flex-shrink:0;display:inline-flex;align-items:center;gap:5px;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
          Edit Profile
        </a>
      </div>

      {{-- Profile fields --}}
      <div class="info-row">
        @php
          $fields = [
            'Father\'s Name'    => $profile?->father_name,
            'Mother\'s Name'    => $profile?->mother_name,
            'Guardian'          => $profile?->guardian_name . ($profile?->guardian_mobile ? ' · '.$profile->guardian_mobile : ''),
            'Guardian Relation' => $profile?->guardian_relation,
            'Date of Birth'     => $profile?->dob?->format('d M Y'),
            'Gender'            => $profile?->gender,
            'Category'          => $profile?->category,
            'Religion'          => $profile?->religion,
            'Nationality'       => $profile?->nationality,
            'Blood Group'       => $profile?->blood_group,
            'Aadhar No.'        => $profile?->aadhar_no,
            'PAN No.'           => $profile?->pan_no,
            'Qualification'     => $profile?->qualification,
            'WhatsApp'          => $profile?->whatsapp_no,
            'Alt. Mobile'       => $profile?->alternate_mobile,
            'State'             => $profile?->state,
            'District'          => $profile?->district,
            'City'              => $profile?->city,
            'PIN Code'          => $profile?->pin_code,
          ];
        @endphp
        @foreach($fields as $lbl => $val)
          @if($val)
            <div class="info-cell">
              <div class="info-lbl">{{ $lbl }}</div>
              <div class="info-val">{{ $val }}</div>
            </div>
          @endif
        @endforeach

        @if($profile?->address)
          <div class="info-cell" style="grid-column:1/-1">
            <div class="info-lbl">Present Address</div>
            <div class="info-val">{{ $profile->address }}</div>
          </div>
        @endif
        @if($profile?->permanent_address)
          <div class="info-cell" style="grid-column:1/-1">
            <div class="info-lbl">Permanent Address</div>
            <div class="info-val">{{ $profile->permanent_address }}</div>
          </div>
        @endif
      </div>

    </div>
  </div>

  {{-- ── Right: Enrollments + Wallet ── --}}
  <div style="display:flex;flex-direction:column;gap:16px;">

    {{-- Wallet --}}
    <div class="gt-card">
      <div class="gt-card-header">
        <div class="gt-card-title">Wallet Balance</div>
        <span class="mono fw-700 {{ $bal >= 0 ? 'amount-pos' : 'amount-neg' }}" style="font-size:18px;">
          ₹{{ number_format(abs($bal), 2) }}
        </span>
      </div>
      <div style="font-size:12px;color:var(--text-2)">
        {{ $bal < 0 ? 'Amount due from student.' : ($bal > 0 ? 'Advance / credit available.' : 'No balance.') }}
      </div>
    </div>

    {{-- Course Enrollments --}}
    <div class="gt-card">
      <div class="gt-card-header">
        <div class="gt-card-title">Enrollments</div>
        <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-outline btn-xs">+ New</a>
      </div>

      @forelse($enrollments as $e)
        <div class="enroll-card">
          {{-- Course + status --}}
          <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
            <div>
              <div style="font-weight:700;font-size:14px;">{{ $e->course?->name }}</div>
              <div style="font-size:12px;color:var(--text-2);margin-top:2px;">
                {{ $e->batch?->name ?? 'No Batch' }}
                &nbsp;·&nbsp;
                <code style="font-size:11px;color:var(--text-2)">{{ $e->enrollment_no ?: 'Pending enroll. no.' }}</code>
              </div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
              <span class="badge {{ match($e->status){
                'RUN'     => 'badge-success',
                'OPEN'    => 'badge-warning',
                'CLOSE'   => 'badge-neutral',
                default   => 'badge-danger'
              } }}">{{ $e->status === 'OPEN' ? 'SEAT BOOKED' : $e->status }}</span>
              <div style="font-size:12px;font-weight:700;margin-top:4px;color:var(--text)">
                ₹{{ number_format($e->final_fee, 2) }}
              </div>
            </div>
          </div>

          {{-- Expired notice --}}
          @if($e->status === 'EXPIRED')
            <div style="margin-top:10px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;font-size:12px;color:#b91c1c;display:flex;align-items:center;gap:8px;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              Seat booking expired. Renew to continue with admission.
            </div>
          @endif

          {{-- Fee summary (if RUN) --}}
          @if($e->status === 'RUN')
            @php
              $amtCol  = \App\Models\FeeCollectDetail::amountColumn();
              $paid    = (float)\App\Models\FeeCollectDetail::where('course_book_id',$e->id)->whereNull('cancelled_at')->sum($amtCol);
              $due     = max($e->final_fee - $paid, 0);
            @endphp
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-top:10px;">
              <div style="background:var(--bg-3);border-radius:8px;padding:7px 10px;text-align:center;">
                <div style="font-size:10px;color:var(--text-2);font-weight:700;text-transform:uppercase;">Total</div>
                <div style="font-size:13px;font-weight:800;">₹{{ number_format($e->final_fee,2) }}</div>
              </div>
              <div style="background:#f0fdf4;border-radius:8px;padding:7px 10px;text-align:center;">
                <div style="font-size:10px;color:#15803d;font-weight:700;text-transform:uppercase;">Paid</div>
                <div style="font-size:13px;font-weight:800;color:#16a34a;">₹{{ number_format($paid,2) }}</div>
              </div>
              <div style="background:{{ $due>0?'#fef2f2':'var(--bg-3)' }};border-radius:8px;padding:7px 10px;text-align:center;">
                <div style="font-size:10px;color:{{ $due>0?'#b91c1c':'var(--text-2)' }};font-weight:700;text-transform:uppercase;">Due</div>
                <div style="font-size:13px;font-weight:800;color:{{ $due>0?'#dc2626':'var(--text)' }};">₹{{ number_format($due,2) }}</div>
              </div>
            </div>
          @endif

          {{-- Action buttons --}}
          <div class="enroll-actions">
            @if($e->status === 'EXPIRED')
              {{-- Expired: Renew or Cancel --}}
              <form method="POST" action="{{ route('institute.enrollment.renew', $e) }}"
                    class="renew-form" style="margin:0;" data-no-spinner>
                @csrf
                <button type="button" class="btn btn-primary btn-xs renew-btn"
                        style="display:inline-flex;align-items:center;gap:4px;">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 4 23 10 17 10"/>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                  </svg>
                  Renew Booking
                </button>
              </form>
              <form method="POST" action="{{ route('institute.enrollment.cancel', $e) }}"
                    class="cancel-form" style="margin:0;" data-no-spinner>
                @csrf
                <button type="button" class="btn btn-outline btn-xs cancel-btn"
                        style="display:inline-flex;align-items:center;gap:4px;color:#dc2626;border-color:#fca5a5;">
                  Cancel
                </button>
              </form>
            @elseif($e->status === 'OPEN')
              {{-- Open: Fill Details / Collect Fee + Cancel --}}
              <a href="{{ route('institute.enrollment.profile', $e) }}" class="btn btn-outline btn-xs" style="display:inline-flex;align-items:center;gap:4px;">Fill Details</a>
              <a href="{{ route('institute.enrollment.fee', $e) }}" class="btn btn-primary btn-xs" style="display:inline-flex;align-items:center;gap:4px;">Collect Fee</a>
              <form method="POST" action="{{ route('institute.enrollment.cancel', $e) }}"
                    class="cancel-form" style="margin:0;" data-no-spinner>
                @csrf
                <button type="button" class="btn btn-outline btn-xs cancel-btn"
                        style="display:inline-flex;align-items:center;gap:4px;color:#dc2626;border-color:#fca5a5;">
                  Cancel
                </button>
              </form>
            @else
              {{-- Edit Details --}}
              <a href="{{ route('institute.students.edit', $student) }}"
                 class="btn btn-outline btn-xs"
                 style="display:inline-flex;align-items:center;gap:4px;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit Details
              </a>

              {{-- Collect Fee --}}
              <a href="{{ route('institute.enrollment.fee', $e) }}"
                 class="btn btn-primary btn-xs"
                 style="display:inline-flex;align-items:center;gap:4px;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="1" x2="12" y2="23"/>
                  <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                Collect Fee
              </a>

              {{-- Download Application Form --}}
              <a href="{{ route('institute.enrollment.preview', $e) }}"
                 class="btn btn-outline btn-xs"
                 style="display:inline-flex;align-items:center;gap:4px;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                  <line x1="12" y1="18" x2="12" y2="12"/>
                  <line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
                Application Form
              </a>

              {{-- Change Course / Batch --}}
              <a href="{{ route('institute.students.enrollments.edit', [$student, $e]) }}"
                 class="btn btn-outline btn-xs"
                 style="display:inline-flex;align-items:center;gap:4px;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="16 3 21 3 21 8"/>
                  <line x1="4" y1="20" x2="21" y2="3"/>
                  <polyline points="21 16 21 21 16 21"/>
                  <line x1="15" y1="4" x2="4" y2="15"/>
                </svg>
                Change Course
              </a>
            @endif
          </div>
        </div>
      @empty
        <div style="padding:24px;text-align:center;color:var(--text-2);font-size:13px;">
          No enrollments yet.
        </div>
      @endforelse
    </div>

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
      html: 'This booking will be marked as <b>Cancelled</b>. The student will be moved to the Cancelled list.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, Cancel Booking',
      cancelButtonText: 'No',
      confirmButtonColor: '#dc2626',
      reverseButtons: true,
    }).then(result => {
      if (result.isConfirmed) {
        btn.disabled = true;
        btn.textContent = 'Cancelling…';
        form.submit();
      }
    });
  });
});

document.querySelectorAll('.renew-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const form = this.closest('.renew-form');
    Swal.fire({
      title: 'Renew Booking?',
      html: 'The booking date will be reset to <b>today</b> and this student will move back to <b>Pending Admissions</b>.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, Renew',
      cancelButtonText: 'Cancel',
      confirmButtonColor: 'var(--accent)',
      reverseButtons: true,
    }).then(result => {
      if (result.isConfirmed) {
        btn.disabled = true;
        btn.innerHTML = '<span class="gt-btn-spinner"></span> Renewing…';
        form.submit();
      }
    });
  });
});
</script>
@endpush
