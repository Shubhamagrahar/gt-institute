@extends('layouts.institute')
@section('title', $student->name)
@section('page-title', $student->name)
@section('topbar-actions')
  <a href="{{ route('institute.students.edit',$student) }}" class="btn btn-outline btn-sm">Edit</a>
  <a href="{{ route('institute.students.ledger',$student) }}" class="btn btn-outline btn-sm">Ledger</a>
  <a href="{{ route('institute.fee.history',$student) }}" class="btn btn-outline btn-sm">Fee History</a>
@endsection
@section('content')
<div class="gt-grid-2" style="gap:20px;align-items:start;">
  <div class="gt-card">
    <div class="gt-card-header">
      <div>
        <div class="gt-card-title">{{ $student->name }}</div>
        <div class="text-xs text-muted" style="margin-top:2px;"><code style="color:var(--accent);">{{ $student->user_id }}</code></div>
      </div>
      <span class="badge {{ $student->status==='active'?'badge-success':'badge-danger' }}">{{ ucfirst($student->status) }}</span>
    </div>
    <table class="gt-table">
      <tr><td class="text-muted" style="width:140px;">Mobile</td><td class="mono">{{ $student->mobile }}</td></tr>
      <tr><td class="text-muted">Email</td><td>{{ $student->email ?? '—' }}</td></tr>
      @php $sp = $student->studentProfile; @endphp
      @if($sp)
      <tr><td class="text-muted">Father</td><td>{{ $sp->father_name ?? '—' }} {{ $sp->father_mobile ? '· '.$sp->father_mobile : '' }}</td></tr>
      <tr><td class="text-muted">Mother</td><td>{{ $sp->mother_name ?? '—' }}</td></tr>
      <tr><td class="text-muted">DOB</td><td>{{ $sp->dob ? date('d M Y',strtotime($sp->dob)) : '—' }}</td></tr>
      <tr><td class="text-muted">Gender</td><td>{{ $sp->gender ?? '—' }}</td></tr>
      <tr><td class="text-muted">Qualification</td><td>{{ $sp->qualification ?? '—' }}</td></tr>
      <tr><td class="text-muted">State</td><td>{{ $sp->state ?? '—' }}</td></tr>
      <tr><td class="text-muted">PIN</td><td>{{ $sp->pin_code ?? '—' }}</td></tr>
      <tr><td class="text-muted">Address</td><td>{{ $sp->full_add ?? '—' }}</td></tr>
      <tr><td class="text-muted">Fee Type</td><td><span class="badge badge-info">{{ $sp->fee_collect_type }}</span></td></tr>
      @if($sp->fee_collect_type === 'MONTHLY')
      <tr><td class="text-muted">Monthly Fee</td><td class="mono">₹{{ number_format($sp->monthly_fee,2) }}</td></tr>
      @endif
      <tr><td class="text-muted">Reg Date</td><td>{{ $sp->r_date ? date('d M Y',strtotime($sp->r_date)) : '—' }}</td></tr>
      @endif
    </table>
  </div>

  <div>
    <div class="gt-card mb-3">
      <div class="gt-card-header">
        <div class="gt-card-title">Wallet Balance</div>
        @php $bal = $student->wallet?->main_b ?? 0; @endphp
        <span class="mono fw-700 {{ $bal >= 0 ? 'amount-pos' : 'amount-neg' }}" style="font-size:18px;">₹{{ number_format($bal,2) }}</span>
      </div>
      <a href="{{ route('institute.fee.index') }}" class="btn btn-success w-full" style="justify-content:center;">Collect Fee</a>
    </div>

    <div class="gt-card">
      <div class="gt-card-header">
        <div class="gt-card-title">Enrolled Courses</div>
        <a href="{{ route('institute.courses.enrollments') }}" class="btn btn-outline btn-xs">Enroll</a>
      </div>
      @forelse($enrollments as $e)
      <div style="padding:10px 0;border-bottom:1px solid var(--border);">
        <div class="flex justify-between items-center">
          <div>
            <div class="fw-600 text-sm">{{ $e->course->name }}</div>
            <div class="text-xs text-muted">{{ $e->batch?->name ?? 'No batch' }} · Started {{ $e->start_date ? date('d M Y',strtotime($e->start_date)) : '—' }}</div>
          </div>
          <div class="text-right">
            <span class="badge {{ match($e->status){ 'RUN'=>'badge-success','OPEN'=>'badge-warning','CLOSE'=>'badge-neutral',default=>'badge-danger' } }}">{{ $e->status }}</span>
            <div class="mono text-sm" style="margin-top:3px;">₹{{ number_format($e->fee,2) }}</div>
          </div>
        </div>
      </div>
      @empty
      <div class="gt-empty" style="padding:20px 0;">
        <div class="gt-empty-title">No courses enrolled</div>
      </div>
      @endforelse
    </div>
  </div>
</div>
@endsection
