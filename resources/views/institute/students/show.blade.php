@extends('layouts.institute')
@section('title', $student->profile?->name ?? $student->user_id)
@section('page-title', $student->profile?->name ?? $student->user_id)
@section('topbar-actions')
  <a href="{{ route('institute.students.edit',$student) }}" class="btn btn-outline btn-sm">Edit Profile</a>
  <a href="{{ route('institute.students.ledger',$student) }}" class="btn btn-outline btn-sm">Ledger</a>
  <a href="{{ route('institute.fee-collect.show',$student) }}" class="btn btn-outline btn-sm">Fee Collection</a>
@endsection

@section('content')
@php
  $profile = $student->profile;
  $wallet = $student->studentWallet;
  $bal = $wallet?->balance ?? 0;
@endphp
<div class="gt-grid-2" style="gap:20px;align-items:start;">
  <div class="gt-card">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title">{{ $profile?->name ?? $student->user_id }}</div>
        <div class="text-xs text-muted" style="margin-top:2px;"><code style="color:var(--accent);">{{ $student->user_id }}</code></div>
      </div>
      <span class="badge {{ $student->status==='active'?'badge-success':'badge-danger' }}">{{ ucfirst($student->status) }}</span>
    </div>
    <table class="gt-table">
      <tr><td class="text-muted" style="width:160px;">Mobile</td><td class="mono">{{ $student->mobile }}</td></tr>
      <tr><td class="text-muted">Email</td><td>{{ $student->email ?? '—' }}</td></tr>
      <tr><td class="text-muted">Enrollment No</td><td class="mono">{{ $student->enrollment_no ?? 'Pending' }}</td></tr>
      <tr><td class="text-muted">Father</td><td>{{ $profile?->father_name ?? '—' }}</td></tr>
      <tr><td class="text-muted">Mother</td><td>{{ $profile?->mother_name ?? '—' }}</td></tr>
      <tr><td class="text-muted">Guardian</td><td>{{ $profile?->guardian_name ?? '—' }}{{ $profile?->guardian_mobile ? ' · '.$profile->guardian_mobile : '' }}</td></tr>
      <tr><td class="text-muted">DOB</td><td>{{ $profile?->dob?->format('d M Y') ?? '—' }}</td></tr>
      <tr><td class="text-muted">Gender</td><td>{{ $profile?->gender ?? '—' }}</td></tr>
      <tr><td class="text-muted">Qualification</td><td>{{ $profile?->qualification ?? '—' }}</td></tr>
      <tr><td class="text-muted">State</td><td>{{ $profile?->state ?? '—' }}</td></tr>
      <tr><td class="text-muted">District</td><td>{{ $profile?->district ?? '—' }}</td></tr>
      <tr><td class="text-muted">Address</td><td>{{ $profile?->address ?? '—' }}</td></tr>
      <tr><td class="text-muted">Permanent Address</td><td>{{ $profile?->permanent_address ?? '—' }}</td></tr>
    </table>
  </div>

  <div style="display:flex;flex-direction:column;gap:20px;">
    <div class="gt-card">
      <div class="gt-card-header">
        <div class="gt-card-title">Wallet Balance</div>
        <span class="mono fw-700 {{ $bal >= 0 ? 'amount-pos' : 'amount-neg' }}" style="font-size:18px;">₹{{ number_format(abs($bal),2) }}</span>
      </div>
      <div class="text-xs text-muted">Negative balance ka matlab due, positive ka matlab advance.</div>
    </div>

    <div class="gt-card">
      <div class="gt-card-header">
        <div class="gt-card-title">Course Bookings / Admissions</div>
        <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-outline btn-xs">New Booking</a>
      </div>
      @forelse($enrollments as $e)
        <div style="padding:12px 0;border-bottom:1px solid var(--border);">
          <div class="flex justify-between items-start" style="gap:12px;">
            <div>
              <div class="fw-600 text-sm">{{ $e->course?->name }}</div>
              <div class="text-xs text-muted">{{ $e->batch?->name ?? 'No batch' }} · {{ strtoupper($e->booking_mode ?? 'full') }}</div>
              <div class="text-xs mono text-muted" style="margin-top:4px;">{{ $e->enrollment_no ?: 'Enrollment no after final admission' }}</div>
            </div>
            <div class="text-right">
              <span class="badge {{ match($e->status){ 'RUN'=>'badge-success','OPEN'=>'badge-warning','CLOSE'=>'badge-neutral',default=>'badge-danger' } }}">{{ $e->status === 'OPEN' ? 'SEAT BOOKED' : $e->status }}</span>
              <div class="mono text-sm" style="margin-top:3px;">₹{{ number_format($e->final_fee,2) }}</div>
            </div>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
            <a href="{{ route('institute.students.enrollments.edit', [$student, $e]) }}" class="btn btn-outline btn-xs">Change Course / Batch</a>
            @if($e->status === 'OPEN')
              <a href="{{ route('institute.enrollment.profile', $e) }}" class="btn btn-outline btn-xs">Complete Admission</a>
            @endif
          </div>
        </div>
      @empty
        <div class="gt-empty" style="padding:20px 0;">
          <div class="gt-empty-title">No course bookings yet</div>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection
