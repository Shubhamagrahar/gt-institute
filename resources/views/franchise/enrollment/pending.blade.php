@extends('layouts.franchise')
@section('title','Pending Admissions')
@section('page-title','Admissions')

@push('styles')
<style>
.adm-tabs{display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:20px}
.adm-tab{padding:10px 22px;font-weight:700;font-size:13px;cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;color:var(--text-2);transition:.15s;white-space:nowrap}
.adm-tab.active{color:var(--primary);border-bottom-color:var(--primary)}
.adm-section{display:none}.adm-section.active{display:block}

.student-card{display:grid;grid-template-columns:56px 1fr auto;gap:14px;align-items:center;padding:14px 18px;border-bottom:1px solid var(--border);transition:.15s}
.student-card:last-child{border-bottom:none}
.student-card:hover{background:var(--bg-3)}
.stu-avatar{width:48px;height:48px;border-radius:50%;object-fit:cover;background:var(--bg-3);border:2px solid var(--border)}
.stu-name{font-weight:700;font-size:14px}
.stu-sub{font-size:12px;color:var(--text-2);margin-top:2px}
.stu-badges{display:flex;gap:5px;flex-wrap:wrap;margin-top:5px}

.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px}
@media(max-width:768px){.stats-row{grid-template-columns:repeat(2,1fr)}.student-card{grid-template-columns:40px 1fr}}
.stat-box{background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:14px 18px}
.stat-num{font-size:28px;font-weight:900}
.stat-label{font-size:12px;color:var(--text-2);margin-top:2px}
</style>
@endpush

@section('topbar-actions')
  <a href="{{ route('franchise.enrollment.new') }}" class="btn btn-primary btn-sm">+ New Admission</a>
@endsection

@section('content')
@php
  $totalOpen      = $openBooks->count();
  $totalAdmitted  = $admittedBooks->count();
  $detailsPending = $openBooks->where('details_complete', false)->count();
  $readyForAdmission = $openBooks->where('admission_ready', true)->count();
@endphp

{{-- Stats --}}
<div class="stats-row">
  <div class="stat-box">
    <div class="stat-num">{{ $totalOpen }}</div>
    <div class="stat-label">Seat Booked (Pending)</div>
  </div>
  <div class="stat-box">
    <div class="stat-num" style="color:#ea580c">{{ $totalAdmitted }}</div>
    <div class="stat-label">Admitted Students</div>
  </div>
  <div class="stat-box">
    <div class="stat-num" style="color:#dc2626">{{ $detailsPending }}</div>
    <div class="stat-label">Details Incomplete</div>
  </div>
  <div class="stat-box">
    <div class="stat-num" style="color:#d97706">{{ $readyForAdmission }}</div>
    <div class="stat-label">Ready for Admission</div>
  </div>
</div>

{{-- Tabs --}}
<div class="adm-tabs">
  <div class="adm-tab active" onclick="switchTab('open',this)">
    Seat Booked
    @if($totalOpen > 0)<span class="badge badge-warning" style="margin-left:6px">{{ $totalOpen }}</span>@endif
  </div>
  <div class="adm-tab" onclick="switchTab('admitted',this)">
    Admitted
    @if($totalAdmitted > 0)<span class="badge badge-success" style="margin-left:6px">{{ $totalAdmitted }}</span>@endif
  </div>
</div>

{{-- OPEN section --}}
<div class="adm-section active" id="tab-open">
  <div class="gt-card">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title">Seat Booked — Pending Admission</div>
        <div class="text-xs text-muted">Click "Process Admission" to complete profile and fee setup.</div>
      </div>
      <a href="{{ route('franchise.enrollment.new') }}" class="btn btn-primary btn-sm">+ New Booking</a>
    </div>

    @if($openBooks->isEmpty())
      <div class="gt-empty">
        <div class="gt-empty-title">No pending bookings</div>
        <div class="gt-empty-sub">Add a new student to get started.</div>
      </div>
    @else
      @foreach($openBooks as $book)
      @php $photo = $book->student->profile?->photo ?? 'images/user.svg'; @endphp
      <div class="student-card">
        <img src="{{ asset($photo) }}" class="stu-avatar" alt="photo" onerror="this.src='{{ asset('images/user.svg') }}'">
        <div>
          <div class="stu-name">{{ $book->student->profile?->name ?? $book->student->user_id }}</div>
          <div class="stu-sub">
            {{ $book->student->mobile }}
            @if($book->student->email) &middot; {{ $book->student->email }}@endif
          </div>
          <div class="stu-sub">
            {{ $book->course?->name }}
            @if($book->batch) &middot; {{ $book->batch->name }}@endif
            &middot; Booked {{ $book->book_date?->format('d M Y') ?? '' }}
          </div>
          <div class="stu-badges">
            @if(!$book->details_complete)
              <span class="badge badge-danger">Details Pending</span>
            @else
              <span class="badge badge-success">Details Complete</span>
            @endif
            @if($book->admission_ready)
              <span class="badge badge-success">Ready</span>
            @elseif($book->paid_amount > 0)
              <span class="badge badge-warning">Partial Payment</span>
            @endif
          </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end">
          <a href="{{ route('franchise.enrollment.profile', $book) }}" class="btn btn-primary btn-sm">
            Process Admission
          </a>
          @if($book->paid_amount > 0)
            <div class="text-xs mono text-muted">₹{{ number_format($book->paid_amount, 2) }} paid</div>
          @endif
        </div>
      </div>
      @endforeach
    @endif
  </div>
</div>

{{-- ADMITTED section --}}
<div class="adm-section" id="tab-admitted">
  <div class="gt-card">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title">Admitted Students</div>
        <div class="text-xs text-muted">Click "Collect Fee" to manage payments.</div>
      </div>
    </div>

    @if($admittedBooks->isEmpty())
      <div class="gt-empty">
        <div class="gt-empty-title">No admitted students yet</div>
        <div class="gt-empty-sub">Process pending bookings to see admitted students here.</div>
      </div>
    @else
      @foreach($admittedBooks as $book)
      @php
        $photo = $book->student->profile?->photo ?? 'images/user.svg';
        $due   = max(round($book->final_fee - $book->paid_amount, 2), 0);
      @endphp
      <div class="student-card">
        <img src="{{ asset($photo) }}" class="stu-avatar" alt="photo" onerror="this.src='{{ asset('images/user.svg') }}'">
        <div>
          <div class="stu-name">{{ $book->student->profile?->name ?? $book->student->user_id }}</div>
          <div class="stu-sub mono" style="color:var(--primary)">{{ $book->enrollment_no ?? '—' }}</div>
          <div class="stu-sub">{{ $book->course?->name }} &middot; {{ $book->plan_code ?? 'N/A' }}</div>
          <div class="stu-badges">
            <span class="badge badge-success">ADMITTED</span>
            @if($due > 0)
              <span class="badge badge-danger">Due ₹{{ number_format($due, 2) }}</span>
            @else
              <span class="badge badge-success">Fully Paid</span>
            @endif
          </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end">
          <a href="{{ route('franchise.enrollment.payment-complete', $book) }}" class="btn btn-primary btn-sm">
            Collect Fee
          </a>
          <a href="{{ route('franchise.enrollment.profile', $book) }}" class="btn btn-outline btn-sm">
            Profile
          </a>
        </div>
      </div>
      @endforeach
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(id, el) {
  document.querySelectorAll('.adm-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.adm-section').forEach(s => s.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('tab-' + id).classList.add('active');
}
</script>
@endpush
