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

.course-filter-bar {
  display: flex;
  align-items: center;
  gap: 10px;
}

.course-filter-select-wrap,
.course-search-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.course-filter-icon,
.course-search-icon {
  position: absolute;
  left: 10px;
  width: 14px;
  height: 14px;
  color: var(--text-3);
  pointer-events: none;
  flex-shrink: 0;
}

.course-filter-select {
  height: 36px;
  padding: 0 12px 0 30px;
  border: 1px solid var(--border);
  border-radius: 8px;
  background: var(--bg);
  color: var(--text);
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  outline: none;
  min-width: 170px;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%2394a3b8'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 14px;
  padding-right: 30px;
  transition: border-color .15s, box-shadow .15s;
}

.course-filter-select:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(108,93,211,.12);
}

.course-search-input {
  height: 36px;
  padding: 0 12px 0 32px;
  border: 1px solid var(--border);
  border-radius: 8px;
  background: var(--bg);
  color: var(--text);
  font-size: 13px;
  outline: none;
  width: 220px;
  transition: border-color .15s, box-shadow .15s;
}

.course-search-input::placeholder { color: var(--text-3); }

.course-search-input:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(108,93,211,.12);
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
  const searchInput  = document.getElementById('table-search');
  const typeFilter   = document.getElementById('type-filter');
  const visibleCount = document.getElementById('visible-count');
  const rows         = document.querySelectorAll('.course-list-table tbody tr[data-type-id]');

  function applyFilters() {
    const term     = searchInput.value.toLowerCase().trim();
    const typeId   = typeFilter.value;
    let count      = 0;
    rows.forEach(function (row) {
      const text     = row.textContent.toLowerCase();
      const rowType  = row.dataset.typeId || '';
      const matchText = !term || text.includes(term);
      const matchType = !typeId || rowType === typeId;
      const visible  = matchText && matchType;
      row.style.display = visible ? '' : 'none';
      if (visible) count++;
    });
    visibleCount.textContent = count;
  }

  searchInput.addEventListener('input', applyFilters);
  typeFilter.addEventListener('change', applyFilters);

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
    <div class="gt-card-title">All Courses (<span id="visible-count">{{ $courses->count() }}</span>)</div>
    <div class="course-filter-bar">
      <div class="course-filter-select-wrap">
        <svg class="course-filter-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 01.707 1.707L13 11.414V16a1 1 0 01-.553.894l-4 2A1 1 0 017 18v-6.586L3.293 4.707A1 1 0 013 4z" clip-rule="evenodd"/></svg>
        <select id="type-filter" class="course-filter-select">
          <option value="">All Course Types</option>
          @foreach($courseTypes as $type)
            <option value="{{ $type->id }}">{{ $type->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="course-search-wrap">
        <svg class="course-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
        <input type="text" id="table-search" class="course-search-input" placeholder="Search course, code, type...">
      </div>
    </div>
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
          <th>Extra Fees</th>
          <th>Action</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($courses as $index => $c)
        <tr data-type-id="{{ $c->course_type_id }}">
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
              <div class="course-price-sub">Max Fee: Rs. {{ number_format($c->display_max_fee, 2) }}</div>
            </div>
          </td>
          <td>
            @if($c->feeStructures->isNotEmpty())
              <div class="course-sub">
                {{ $c->feeStructures->map(fn($row) => $row->fee_type_name . ' Rs. ' . number_format($row->amount, 2))->join(', ') }}
              </div>
            @else
              <div class="course-sub">No extra fees</div>
            @endif
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
          <td colspan="8">
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
