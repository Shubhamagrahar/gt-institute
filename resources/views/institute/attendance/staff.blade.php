@extends('layouts.institute')
@section('title','Staff Attendance')
@section('page-title','Staff Attendance')
@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">Mark Staff Attendance</div>
    <div class="flex gap-2">
      <button id="mark-all-present" class="btn btn-success btn-sm">All Present</button>
      <button id="mark-all-absent" class="btn btn-danger btn-sm">All Absent</button>
    </div>
  </div>
  <form method="POST" action="{{ route('institute.attendance.staff.mark') }}">
    @csrf
    <div class="gt-form-group" style="max-width:220px;margin-bottom:16px;">
      <label class="gt-label">Date</label>
      <input type="date" name="date" class="gt-input" value="{{ $date }}">
    </div>
    <div class="gt-table-wrap">
      <table class="gt-table">
        <thead><tr><th>#</th><th>Staff</th><th>Designation</th><th style="text-align:center;">Present</th><th style="text-align:center;">Absent</th></tr></thead>
        <tbody>
          @forelse($staff as $i => $s)
          <tr>
            <td class="text-muted">{{ $i+1 }}</td>
            <td class="fw-600">{{ $s->name }}</td>
            <td class="text-muted text-sm">{{ $s->staffProfile?->designation ?? '—' }}</td>
            <td style="text-align:center;">
              <input type="radio" class="att-radio-p" name="attendance[{{ $s->id }}]" value="P"
                {{ ($existing[$s->id] ?? 'A') === 'P' ? 'checked' : '' }}
                style="accent-color:var(--success);width:16px;height:16px;">
            </td>
            <td style="text-align:center;">
              <input type="radio" class="att-radio-a" name="attendance[{{ $s->id }}]" value="A"
                {{ ($existing[$s->id] ?? 'A') === 'A' ? 'checked' : '' }}
                style="accent-color:var(--danger);width:16px;height:16px;">
            </td>
          </tr>
          @empty
          <tr><td colspan="5"><div class="gt-empty"><div class="gt-empty-title">No staff found</div></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($staff->count())
    <div style="padding:16px 0;">
      <button type="submit" class="btn btn-primary">Save Staff Attendance</button>
    </div>
    @endif
  </form>
</div>
@endsection
