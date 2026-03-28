@extends('layouts.institute')
@section('title', $staff->name)
@section('page-title', $staff->name)
@section('topbar-actions')
  <a href="{{ route('institute.staff.edit',$staff) }}" class="btn btn-outline btn-sm">Edit</a>
  <a href="{{ route('institute.staff.index') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection
@section('content')
<div class="gt-card" style="max-width:500px;">
  <div class="gt-card-header">
    <div class="gt-card-title">{{ $staff->name }}</div>
    <span class="badge {{ $staff->status==='active'?'badge-success':'badge-danger' }}">{{ ucfirst($staff->status) }}</span>
  </div>
  @php $sp = $staff->staffProfile; @endphp
  <table class="gt-table">
    <tr><td class="text-muted" style="width:140px;">Staff ID</td><td><code style="color:var(--accent);">{{ $staff->user_id }}</code></td></tr>
    <tr><td class="text-muted">Mobile</td><td class="mono">{{ $staff->mobile }}</td></tr>
    <tr><td class="text-muted">Email</td><td>{{ $staff->email ?? '—' }}</td></tr>
    <tr><td class="text-muted">Designation</td><td>{{ $sp?->designation ?? '—' }}</td></tr>
    <tr><td class="text-muted">Salary</td><td class="mono">{{ $sp?->salary ? '₹'.number_format($sp->salary,2) : '—' }}</td></tr>
    <tr><td class="text-muted">Joining Date</td><td>{{ $sp?->joining_date ? date('d M Y',strtotime($sp->joining_date)) : '—' }}</td></tr>
  </table>
</div>
@endsection
