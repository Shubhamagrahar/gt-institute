@extends('layouts.institute')
@section('title','Course Fee Setup')
@section('page-title','Course Fee Setup')
@section('topbar-actions')
  <a href="{{ route('institute.fee-types.index') }}" class="btn btn-outline btn-sm">Fee Types</a>
  <a href="{{ route('institute.courses.create') }}" class="btn btn-primary btn-sm">+ Add Course</a>
@endsection

@section('content')
@push('styles')
<style>
.fee-bindings-toolbar {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  align-items: center;
  margin-bottom: 14px;
}

.fee-bindings-search {
  max-width: 280px;
}

.fee-badge-wrap {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.fee-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 999px;
  background: #eef4ff;
  color: #1746a2;
  font-size: 11px;
  font-weight: 700;
  border: 1px solid #c7d9ff;
}
</style>
@endpush

<div class="gt-card">
  <div class="gt-card-header">
    <div>
      <div class="gt-card-title">Course Fee Setup</div>
      <div class="text-xs text-muted" style="margin-top:4px;">
        Review each course and manage which extra fee heads are attached to it.
      </div>
    </div>
  </div>

  @if($courses->isEmpty())
    <div class="gt-empty">
      <div class="gt-empty-title">No courses found</div>
      <a href="{{ route('institute.courses.create') }}" class="btn btn-primary btn-sm" style="margin-top:8px;">Add First Course</a>
    </div>
  @else
    <div class="fee-bindings-toolbar" style="padding:0 18px;">
      <div class="text-sm text-muted">Search by course name or type.</div>
      <input type="text" id="fee-bindings-search" class="gt-input fee-bindings-search" placeholder="Search bindings...">
    </div>
    <div class="gt-table-wrap">
      <table class="gt-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Course</th>
            <th>Type</th>
            <th>Course Fee</th>
            <th>Extra Fee Types</th>
            <th>Total Fee</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($courses as $i => $course)
            @php
              $extraTotal = $course->feeStructures->sum('amount');
            @endphp
            <tr data-fee-binding-row>
              <td>{{ $i + 1 }}</td>
              <td>
                <div class="fw-700">{{ $course->name }}</div>
                <div class="text-xs text-muted">{{ $course->duration }} months</div>
              </td>
              <td>{{ $course->courseType?->name ?? 'No type' }}</td>
              <td>
                <div class="fw-700">Rs. {{ number_format($course->fee, 2) }}</div>
                <div class="text-xs text-muted">Base course fee</div>
              </td>
              <td>
                @if($course->feeStructures->isNotEmpty())
                  <div class="fee-badge-wrap">
                    @foreach($course->feeStructures as $row)
                      <span class="fee-badge">{{ $row->fee_type_name }}</span>
                    @endforeach
                  </div>
                @else
                  <div class="text-xs text-muted">No extra fee bound</div>
                @endif
              </td>
              <td>
                <div class="fw-700">Rs. {{ number_format($course->fee + $extraTotal, 2) }}</div>
                <div class="text-xs text-muted">Course fee + extra fees</div>
              </td>
              <td>
                <a href="{{ route('institute.courses.fee-bindings.edit', $course) }}" class="btn btn-outline btn-xs">Manage Fees</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('fee-bindings-search');
  const rows = [...document.querySelectorAll('[data-fee-binding-row]')];

  if (!searchInput || !rows.length) return;

  searchInput.addEventListener('input', function () {
    const term = this.value.trim().toLowerCase();

    rows.forEach((row) => {
      row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
  });
});
</script>
@endpush
