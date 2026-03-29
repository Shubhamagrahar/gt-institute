@extends('layouts.institute')
@section('title','Courses')
@section('page-title','Courses')
@section('topbar-actions')
  <a href="{{ route('institute.course-types.index') }}" class="btn btn-outline btn-sm">Course Types</a>
  <a href="{{ route('institute.courses.create') }}" class="btn btn-primary btn-sm">+ Add Course</a>
@endsection

@push('styles')
<style>
.course-list-table .course-name-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.course-list-table .course-thumb {
  width: 64px;
  height: 64px;
  border-radius: 14px;
  object-fit: contain;
  padding: 4px;
  background: #fff;
  border: 1px solid rgba(15, 23, 42, .06);
  flex-shrink: 0;
}

.course-list-table .course-thumb-fallback {
  width: 64px;
  height: 64px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(108,93,211,.12), rgba(59,130,246,.12));
  color: var(--accent);
  font-size: 18px;
  font-weight: 700;
  border: 1px solid rgba(108,93,211,.08);
  flex-shrink: 0;
}

.course-list-table .course-name {
  font-size: 13px;
  font-weight: 700;
  color: var(--text);
}

.course-list-table .course-sub {
  font-size: 11px;
  color: var(--text-3);
  margin-top: 2px;
}

.course-price-wrap { display: flex; flex-direction: column; gap: 3px; }
.course-price-main { font-size: 15px; font-weight: 700; color: var(--accent); }
.course-price-sub { font-size: 12px; color: var(--text-3); }

.status-toggle-form {
  margin: 0;
}

.status-switch {
  position: relative;
  display: inline-flex;
  width: 52px;
  height: 30px;
  border: none;
  background: transparent;
  padding: 0;
  cursor: pointer;
}

.status-switch-track {
  width: 52px;
  height: 30px;
  border-radius: 999px;
  background: #dbe4f0;
  transition: all .2s ease;
  box-shadow: inset 0 0 0 1px rgba(15,23,42,.06);
}

.status-switch-thumb {
  position: absolute;
  top: 3px;
  left: 3px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 2px 8px rgba(15,23,42,.15);
  transition: all .2s ease;
}

.status-switch.is-active .status-switch-track {
  background: rgb(108 93 211);
}

.status-switch.is-active .status-switch-thumb {
  left: 25px;
}

.status-text {
  font-size: 12px;
  font-weight: 600;
  color: var(--text-2);
  margin-top: 6px;
}

.desc-view-btn {
  min-width: 72px;
  justify-content: center;
}

.serial-badge {
  width: 28px;
  height: 28px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-3);
  border: 1px solid var(--border);
  font-size: 11px;
  font-weight: 700;
  color: var(--text-2);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-course-toggle]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const courseName = this.dataset.courseName || 'this course';
      const nextStatus = this.dataset.nextStatus || 'inactive';

      Swal.fire({
        title: 'Change course status?',
        text: `Do you want to mark ${courseName} as ${nextStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#6c5dd3',
        cancelButtonColor: '#94a3b8',
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });

  document.querySelectorAll('[data-description-view]').forEach(function (button) {
    button.addEventListener('click', function () {
      const courseName = this.dataset.courseName || 'Course';
      const description = this.dataset.description || 'No description available.';

      Swal.fire({
        text: description,
        confirmButtonText: 'Close',
        confirmButtonColor: '#6c5dd3',
      });
    });
  });
});
</script>
@endpush

@section('content')
<div class="gt-card">
  <div class="gt-card-header">
    <div class="gt-card-title">All Courses ({{ $courses->count() }})</div>
    <input type="text" id="table-search" class="gt-input" style="max-width:240px;" placeholder="Search course, code, type...">
  </div>
  <div class="gt-table-wrap">
    <table class="gt-table course-list-table">
      <thead>
        <tr>
          <th>Sr.</th>
          <th>Course</th>
          <th>Code / Type</th>
          <th>Duration</th>
          <th>Pricing</th>
          <th>Action</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($courses as $index => $c)
        <tr>
          <td><span class="serial-badge">{{ $index + 1 }}</span></td>
          <td>
            <div class="course-name-cell">
              @if($c->image)
                <img src="{{ asset($c->image) }}" alt="{{ $c->name }}" class="course-thumb">
              @else
                <div class="course-thumb-fallback">{{ strtoupper(substr($c->course_short_name ?: $c->name, 0, 1)) }}</div>
              @endif
              <div>
                <div class="course-name">{{ $c->name }}</div>
                <div class="course-sub">{{ $c->course_short_name ?: 'Short name not set' }}</div>
              </div>
            </div>
          </td>
          <td>
            <div class="fw-600">{{ $c->course_code ?: 'NA' }}</div>
            <div class="course-sub">{{ $c->courseType?->name ?: 'No type selected' }}</div>
          </td>
          <td>
            <div class="fw-600">{{ $c->duration }} months</div>
          </td>
          <td>
            <div class="course-price-wrap">
              <div class="course-price-main">Fee: Rs. {{ number_format($c->fee, 2) }}</div>
              <div class="course-price-sub">Max Fee: Rs. {{ number_format($c->max_fee, 2) }}</div>
            </div>
          </td>
          <td>
            <div class="flex gap-2">
              <a href="{{ route('institute.courses.edit', $c) }}" class="btn btn-outline btn-xs">Edit</a>
              <button
                type="button"
                class="btn btn-outline btn-xs desc-view-btn"
                data-description-view
                data-course-name="{{ $c->name }}"
                data-description="{{ $c->description }}"
              >
                View
              </button>
            </div>
          </td>
          <td>
            <form
              action="{{ route('institute.courses.toggle', $c) }}"
              method="POST"
              class="status-toggle-form"
              data-course-toggle
              data-course-name="{{ $c->name }}"
              data-next-status="{{ $c->status === 'active' ? 'inactive' : 'active' }}"
            >
              @csrf
              @method('PATCH')
              <button type="submit" class="status-switch {{ $c->status === 'active' ? 'is-active' : '' }}" aria-label="Toggle status for {{ $c->name }}">
                <span class="status-switch-track"></span>
                <span class="status-switch-thumb"></span>
              </button>
              <div class="status-text">{{ $c->status === 'active' ? 'Active' : 'Inactive' }}</div>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7">
            <div class="gt-empty">
              <div class="gt-empty-icon">Courses</div>
              <div class="gt-empty-title">No courses yet</div>
              <a href="{{ route('institute.courses.create') }}" class="btn btn-primary btn-sm">Add First Course</a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
