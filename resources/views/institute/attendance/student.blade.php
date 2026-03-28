@extends('layouts.institute')
@section('title','Student Attendance')
@section('page-title','Student Attendance')
@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">Mark Attendance</div>
    <div class="flex gap-2">
      <button id="mark-all-present" class="btn btn-success btn-sm">All Present</button>
      <button id="mark-all-absent" class="btn btn-danger btn-sm">All Absent</button>
    </div>
  </div>
  <form method="POST" action="{{ route('institute.attendance.student.mark') }}">
    @csrf
    <div class="gt-form-grid-2" style="margin-bottom:16px;">
      <div class="gt-form-group" style="margin-bottom:0;">
        <label class="gt-label">Date</label>
        <input type="date" name="date" class="gt-input" value="{{ $date }}">
      </div>
      <div class="gt-form-group" style="margin-bottom:0;">
        <label class="gt-label">Batch</label>
        <select name="batch_id" class="gt-select" onchange="this.form.submit()">
          <option value="">— All Students —</option>
          @foreach($batches as $b)
          <option value="{{ $b->id }}" {{ $batch_id == $b->id ? 'selected':'' }}>{{ $b->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="gt-table-wrap">
      <table class="gt-table">
        <thead><tr><th>#</th><th>Student</th><th>Mobile</th><th style="text-align:center;">Present</th><th style="text-align:center;">Absent</th></tr></thead>
        <tbody>
          @forelse($students as $i => $s)
          <tr>
            <td class="text-muted">{{ $i+1 }}</td>
            <td class="fw-600">{{ $s->name }}</td>
            <td class="mono text-sm">{{ $s->mobile }}</td>
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
          <tr><td colspan="5">
            <div class="gt-empty"><div class="gt-empty-title">No students found for this batch</div></div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($students->count())
    <div style="padding:16px 0;">
      <button type="submit" class="btn btn-primary">Save Attendance for {{ date('d M Y', strtotime($date)) }}</button>
    </div>
    @endif
  </form>
</div>
@endsection
