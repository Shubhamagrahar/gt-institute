@extends('layouts.institute')
@section('title','Enrollments')
@section('page-title','Course Enrollments')
@section('content')

{{-- Enroll Form --}}
<div class="gt-card mb-3">
  <div class="gt-card-header"><div class="gt-card-title">New Enrollment</div></div>
  <form method="POST" action="{{ route('institute.courses.enroll', 0) }}" id="enroll-form">
    @csrf
    <div class="gt-form-grid-3" style="align-items:end;">
      <div class="gt-form-group" style="margin-bottom:0;">
        <label class="gt-label">Student <span style="color:var(--danger)">*</span></label>
        <select name="student_id" class="gt-select" id="enroll-student" required>
          <option value="">— Select Student —</option>
          @foreach($students as $s)
          <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->mobile }})</option>
          @endforeach
        </select>
      </div>
      <div class="gt-form-group" style="margin-bottom:0;">
        <label class="gt-label">Course <span style="color:var(--danger)">*</span></label>
        <select name="course_id" class="gt-select" required>
          <option value="">— Select Course —</option>
          @foreach($courses as $c)
          <option value="{{ $c->id }}" data-fee="{{ $c->fee }}">{{ $c->name }} — ₹{{ number_format($c->fee,0) }}</option>
          @endforeach
        </select>
      </div>
      <div class="gt-form-group" style="margin-bottom:0;">
        <label class="gt-label">Batch</label>
        <select name="batch_id" class="gt-select">
          <option value="">— No Batch —</option>
          @foreach($batches as $b)
          <option value="{{ $b->id }}">{{ $b->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="gt-form-grid-3" style="margin-top:12px;align-items:end;">
      <div class="gt-form-group" style="margin-bottom:0;">
        <label class="gt-label">Fee (₹) <span style="color:var(--danger)">*</span></label>
        <input type="number" name="fee" id="enroll-fee" class="gt-input" min="0" step="0.01" required placeholder="Auto-filled from course">
      </div>
      <div class="gt-form-group" style="margin-bottom:0;">
        <label class="gt-label">Book Date <span style="color:var(--danger)">*</span></label>
        <input type="date" name="book_date" class="gt-input" value="{{ date('Y-m-d') }}" required>
      </div>
      <div class="gt-form-group" style="margin-bottom:0;">
        <label class="gt-label">Start Date</label>
        <input type="date" name="start_date" class="gt-input" value="{{ date('Y-m-d') }}">
      </div>
    </div>
    <div style="margin-top:14px;">
      <button type="submit" class="btn btn-primary">Enroll Student</button>
    </div>
  </form>
</div>

{{-- Enrollments List --}}
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Enrollments ({{ $enrollments->total() }})</div>
    <input type="text" id="table-search" class="gt-input" style="max-width:200px;" placeholder="Search...">
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table">
      <thead><tr><th>Student</th><th>Course</th><th>Batch</th><th>Fee</th><th>Start</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($enrollments as $e)
        <tr>
          <td><div class="fw-600">{{ $e->student?->name }}</div><div class="text-xs text-muted">{{ $e->student?->mobile }}</div></td>
          <td class="text-sm">{{ $e->course?->name }}</td>
          <td class="text-muted text-sm">{{ $e->batch?->name ?? '—' }}</td>
          <td class="mono">₹{{ number_format($e->fee,2) }}</td>
          <td class="text-sm">{{ $e->start_date ? date('d M Y',strtotime($e->start_date)) : '—' }}</td>
          <td><span class="badge {{ match($e->status){ 'RUN'=>'badge-success','OPEN'=>'badge-warning','CLOSE'=>'badge-neutral',default=>'badge-danger' } }}">{{ $e->status }}</span></td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="gt-empty"><div class="gt-empty-title">No enrollments yet</div></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="gt-pagination">{{ $enrollments->links() }}</div>
</div>

@push('scripts')
<script>
// Auto-fill fee from course selection
document.querySelector('[name="course_id"]')?.addEventListener('change', function() {
  const opt = this.options[this.selectedIndex];
  const fee = opt.dataset.fee;
  if (fee) document.getElementById('enroll-fee').value = fee;
});
// Fix form action with selected student
document.getElementById('enroll-form')?.addEventListener('submit', function(e) {
  const sid = document.getElementById('enroll-student').value;
  if (!sid) { e.preventDefault(); return; }
  this.action = this.action.replace('/0', '/' + sid);
});
</script>
@endpush
@endsection
