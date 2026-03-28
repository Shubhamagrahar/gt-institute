@extends('layouts.institute')
@section('title','Staff')
@section('page-title','Staff')
@section('topbar-actions')
  <a href="{{ route('institute.staff.create') }}" class="btn btn-primary btn-sm">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Add Staff
  </a>
@endsection
@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Staff ({{ $staff->total() }})</div>
    <input type="text" id="table-search" class="gt-input" style="max-width:220px;" placeholder="Search...">
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead><tr><th>ID</th><th>Name</th><th>Designation</th><th>Mobile</th><th>Salary</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($staff as $s)
        <tr>
          <td><code style="font-size:11px;color:var(--accent);">{{ $s->user_id }}</code></td>
          <td class="fw-600">{{ $s->name }}</td>
          <td class="text-muted">{{ $s->staffProfile?->designation ?? '—' }}</td>
          <td class="mono text-sm">{{ $s->mobile }}</td>
          <td class="mono">{{ $s->staffProfile?->salary ? '₹'.number_format($s->staffProfile->salary,0) : '—' }}</td>
          <td><span class="badge {{ $s->status==='active'?'badge-success':'badge-danger' }}">{{ ucfirst($s->status) }}</span></td>
          <td>
            <div class="flex gap-2">
              <a href="{{ route('institute.staff.show',$s) }}" class="btn btn-outline btn-xs">View</a>
              <a href="{{ route('institute.staff.edit',$s) }}" class="btn btn-outline btn-xs">Edit</a>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7">
          <div class="gt-empty"><div class="gt-empty-icon">👨‍🏫</div><div class="gt-empty-title">No staff yet</div>
          <a href="{{ route('institute.staff.create') }}" class="btn btn-primary btn-sm">Add Staff</a></div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="gt-pagination">{{ $staff->links() }}</div>
</div>
@endsection
