@extends('layouts.institute')
@section('title','Students')
@section('page-title','Students')
@section('topbar-actions')
  <a href="{{ route('institute.students.create') }}" class="btn btn-primary btn-sm">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Add Student
  </a>
@endsection
@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Students ({{ $students->total() }})</div>
    <input type="text" id="table-search" class="gt-input" style="max-width:220px;" placeholder="Search name, mobile...">
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead><tr>
        <th>Reg No</th><th>Name</th><th>Mobile</th><th>Fee Type</th><th>Balance</th><th>Status</th><th>Actions</th>
      </tr></thead>
      <tbody>
        @forelse($students as $s)
        <tr>
          <td><code style="font-size:11px;color:var(--accent);">{{ $s->user_id }}</code></td>
          <td>
            <div class="fw-600">{{ $s->name }}</div>
            <div class="text-xs text-muted">{{ $s->studentProfile?->gender }} {{ $s->studentProfile?->dob ? '· '.date('d M Y',strtotime($s->studentProfile->dob)) : '' }}</div>
          </td>
          <td class="mono text-sm">{{ $s->mobile }}</td>
          <td>
            @php $ft = $s->studentProfile?->fee_collect_type ?? 'OTP'; @endphp
            <span class="badge {{ $ft==='MONTHLY'?'badge-info':($ft==='PART'?'badge-warning':'badge-neutral') }}">{{ $ft }}</span>
          </td>
          <td class="mono">
            @php $bal = $s->wallet?->main_b ?? 0; @endphp
            <span class="{{ $bal >= 0 ? 'amount-pos' : 'amount-neg' }}">₹{{ number_format($bal,2) }}</span>
          </td>
          <td><span class="badge {{ $s->status==='active'?'badge-success':'badge-danger' }}">{{ ucfirst($s->status) }}</span></td>
          <td>
            <div class="flex gap-2">
              <a href="{{ route('institute.students.show',$s) }}" class="btn btn-outline btn-xs">View</a>
              <a href="{{ route('institute.students.ledger',$s) }}" class="btn btn-outline btn-xs">Ledger</a>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7">
          <div class="gt-empty">
            <div class="gt-empty-icon">👨‍🎓</div>
            <div class="gt-empty-title">No students yet</div>
            <a href="{{ route('institute.students.create') }}" class="btn btn-primary btn-sm">Add First Student</a>
          </div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="gt-pagination">{{ $students->links() }}</div>
</div>
@endsection
