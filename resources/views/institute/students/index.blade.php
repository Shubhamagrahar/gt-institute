@extends('layouts.institute')
@section('title','Students')
@section('page-title','Students')
@section('topbar-actions')
  <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-primary btn-sm">New Admission</a>
@endsection

@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Students ({{ $students->total() }})</div>
    <input type="text" id="table-search" class="gt-input" style="max-width:220px;" placeholder="Search name, mobile...">
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead>
        <tr>
          <th>Student ID</th>
          <th>Name</th>
          <th>Mobile</th>
          <th>Admission No</th>
          <th>Balance</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($students as $s)
          @php
            $profile = $s->profile;
            $wallet = $s->studentWallet;
            $bal = $wallet?->balance ?? 0;
          @endphp
          <tr>
            <td><code style="font-size:11px;color:var(--accent);">{{ $s->user_id }}</code></td>
            <td>
              <div class="fw-600">{{ $profile?->name ?? $s->user_id }}</div>
              <div class="text-xs text-muted">{{ $profile?->gender }}{{ $profile?->dob ? ' · '.$profile->dob->format('d M Y') : '' }}</div>
            </td>
            <td class="mono text-sm">{{ $s->mobile }}</td>
            <td class="mono text-sm">{{ $s->current_enrollment_no ?? 'Pending' }}</td>
            <td class="mono">
              <span class="{{ $bal >= 0 ? 'amount-pos' : 'amount-neg' }}">₹{{ number_format(abs($bal),2) }}</span>
            </td>
            <td><span class="badge {{ $s->status === 'active' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($s->status) }}</span></td>
            <td>
              <div class="flex gap-2">
                <a href="{{ route('institute.students.show',$s) }}" class="btn btn-outline btn-xs">View</a>
                <a href="{{ route('institute.students.ledger',$s) }}" class="btn btn-outline btn-xs">Ledger</a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7">
              <div class="gt-empty">
                <div class="gt-empty-title">No students yet</div>
                <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-primary btn-sm">Start Admission</a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="gt-pagination">{{ $students->links() }}</div>
</div>
@endsection
