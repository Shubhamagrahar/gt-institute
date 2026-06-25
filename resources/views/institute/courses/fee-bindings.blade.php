@extends('layouts.institute')
@section('title','Course Fee Setup')
@section('page-title','Course Fee Setup')
@section('topbar-actions')
  <a href="{{ route('institute.fee-types.index') }}" class="btn btn-outline btn-sm">Fee Types</a>
  <a href="{{ route('institute.courses.create') }}" class="btn btn-primary btn-sm">+ Add Course</a>
@endsection

@push('styles')
<style>
/* ── Stats bar ───────────────────────────────────── */
.cfb-stats {
  display: flex;
  gap: 12px;
  padding: 18px 20px 0;
  flex-wrap: wrap;
}

.cfb-stat-card {
  flex: 1;
  min-width: 140px;
  border-radius: 12px;
  padding: 14px 18px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  border: 1px solid transparent;
}

.cfb-stat-card.blue  { background: #eff6ff; border-color: #bfdbfe; }
.cfb-stat-card.purple{ background: #f5f3ff; border-color: #ddd6fe; }
.cfb-stat-card.green { background: #f0fdf4; border-color: #bbf7d0; }
.cfb-stat-card.amber { background: #fffbeb; border-color: #fde68a; }

.cfb-stat-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .5px;
}
.cfb-stat-card.blue   .cfb-stat-label  { color: #3b82f6; }
.cfb-stat-card.purple .cfb-stat-label  { color: #7c3aed; }
.cfb-stat-card.green  .cfb-stat-label  { color: #16a34a; }
.cfb-stat-card.amber  .cfb-stat-label  { color: #d97706; }

.cfb-stat-value {
  font-size: 22px;
  font-weight: 800;
  color: var(--text);
  line-height: 1;
}

.cfb-stat-sub {
  font-size: 11px;
  color: var(--text-3);
  margin-top: 2px;
}

/* ── Toolbar ─────────────────────────────────────── */
.cfb-toolbar {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 16px 20px 12px;
  border-bottom: 1px solid var(--border);
  flex-wrap: wrap;
}

.cfb-filter-wrap,
.cfb-search-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.cfb-filter-icon,
.cfb-search-icon {
  position: absolute;
  left: 10px;
  width: 14px;
  height: 14px;
  color: var(--text-3);
  pointer-events: none;
}

.cfb-filter-select {
  height: 36px;
  padding: 0 30px 0 30px;
  border: 1px solid var(--border);
  border-radius: 8px;
  background: var(--bg);
  color: var(--text);
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  outline: none;
  min-width: 190px;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%2394a3b8'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 14px;
  transition: border-color .15s, box-shadow .15s;
}

.cfb-filter-select:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(108,93,211,.12);
}

.cfb-search-input {
  height: 36px;
  padding: 0 12px 0 32px;
  border: 1px solid var(--border);
  border-radius: 8px;
  background: var(--bg);
  color: var(--text);
  font-size: 13px;
  outline: none;
  width: 240px;
  transition: border-color .15s, box-shadow .15s;
}

.cfb-search-input::placeholder { color: var(--text-3); }

.cfb-search-input:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(108,93,211,.12);
}

.cfb-count {
  margin-left: auto;
  font-size: 12px;
  color: var(--text-3);
  font-weight: 500;
  white-space: nowrap;
}

/* ── Table cells ─────────────────────────────────── */
.cfb-course-name  { font-size: 13px; font-weight: 700; color: var(--text); }
.cfb-course-sub   { font-size: 11px; color: var(--text-3); margin-top: 2px; }

.cfb-type-badge {
  display: inline-flex;
  align-items: center;
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
  background: #f5f3ff;
  color: #6c5dd3;
  border: 1px solid #ddd6fe;
  white-space: nowrap;
}

.cfb-fee-main  { font-size: 14px; font-weight: 700; color: var(--accent); }
.cfb-fee-sub   { font-size: 11px; color: var(--text-3); margin-top: 2px; }

.cfb-total-main { font-size: 14px; font-weight: 800; color: #16a34a; }
.cfb-total-sub  { font-size: 11px; color: var(--text-3); margin-top: 2px; }

.cfb-extra-wrap { display: flex; flex-wrap: wrap; gap: 5px; }

.cfb-extra-badge {
  display: inline-flex;
  align-items: center;
  padding: 3px 9px;
  border-radius: 999px;
  background: #eff6ff;
  color: #1d4ed8;
  font-size: 11px;
  font-weight: 700;
  border: 1px solid #bfdbfe;
}

.cfb-no-extra {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 9px;
  border-radius: 999px;
  background: #f8fafc;
  color: var(--text-3);
  font-size: 11px;
  font-weight: 600;
  border: 1px solid var(--border);
}

.cfb-manage-btn {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  border: 1.5px solid var(--accent);
  color: var(--accent);
  background: transparent;
  text-decoration: none;
  transition: background .15s, color .15s;
  white-space: nowrap;
}

.cfb-manage-btn:hover {
  background: var(--accent);
  color: #fff;
}

.cfb-serial {
  width: 28px;
  height: 28px;
  border-radius: 50%;
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

@section('content')
@php
  $totalCourses   = $courses->count();
  $withExtra      = $courses->filter(fn($c) => $c->feeStructures->isNotEmpty())->count();
  $avgFee         = $totalCourses ? $courses->avg('fee') : 0;
  $maxFee         = $totalCourses ? $courses->max(fn($c) => $c->fee + $c->feeStructures->sum('amount')) : 0;
@endphp

<div class="gt-card">

  {{-- Header --}}
  <div class="gt-card-header" style="border-bottom:none; padding-bottom:0;">
    <div>
      <div class="gt-card-title">Course Fee Setup</div>
      <div class="text-xs text-muted" style="margin-top:3px;">Manage base fees and extra fee heads attached to each course.</div>
    </div>
  </div>

  {{-- Stats --}}
  <div class="cfb-stats">
    <div class="cfb-stat-card blue">
      <div class="cfb-stat-label">Total Courses</div>
      <div class="cfb-stat-value">{{ $totalCourses }}</div>
      <div class="cfb-stat-sub">In fee setup</div>
    </div>
    <div class="cfb-stat-card purple">
      <div class="cfb-stat-label">With Extra Fees</div>
      <div class="cfb-stat-value">{{ $withExtra }}</div>
      <div class="cfb-stat-sub">{{ $totalCourses - $withExtra }} not configured</div>
    </div>
    <div class="cfb-stat-card green">
      <div class="cfb-stat-label">Avg. Course Fee</div>
      <div class="cfb-stat-value">₹{{ number_format($avgFee, 0) }}</div>
      <div class="cfb-stat-sub">Base fee average</div>
    </div>
    <div class="cfb-stat-card amber">
      <div class="cfb-stat-label">Max Total Fee</div>
      <div class="cfb-stat-value">₹{{ number_format($maxFee, 0) }}</div>
      <div class="cfb-stat-sub">Including extra fees</div>
    </div>
  </div>

  @if($courses->isEmpty())
    <div class="gt-empty" style="margin-top:24px;">
      <div class="gt-empty-title">No courses found</div>
      <a href="{{ route('institute.courses.create') }}" class="btn btn-primary btn-sm" style="margin-top:8px;">Add First Course</a>
    </div>
  @else
    {{-- Toolbar --}}
    <div class="cfb-toolbar">
      <div class="cfb-filter-wrap">
        <svg class="cfb-filter-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M5 4a1 1 0 00-.707 1.707L8 9.414V15a1 1 0 00.553.894l4 2A1 1 0 0014 17v-7.586l3.707-3.707A1 1 0 0017 4H5z"/></svg>
        <select id="fee-range-filter" class="cfb-filter-select">
          <option value="">All Fee Ranges</option>
          <option value="0-5000">Under ₹5,000</option>
          <option value="5000-10000">₹5,000 – ₹10,000</option>
          <option value="10000-20000">₹10,000 – ₹20,000</option>
          <option value="20000-99999999">Above ₹20,000</option>
        </select>
      </div>
      <div class="cfb-search-wrap">
        <svg class="cfb-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
        <input type="text" id="fee-bindings-search" class="cfb-search-input" placeholder="Search course name or type...">
      </div>
      <div class="cfb-count">Showing <span id="cfb-visible">{{ $totalCourses }}</span> of {{ $totalCourses }}</div>
    </div>

    {{-- Table --}}
    <div class="gt-table-wrap">
      <table class="gt-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Course</th>
            <th>Course Type</th>
            <th>Course Fee</th>
            <th>Extra Fee Types</th>
            <th>Total Fee</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($courses as $i => $course)
            @php $extraTotal = $course->feeStructures->sum('amount'); @endphp
            <tr data-fee-binding-row data-fee="{{ $course->fee + $extraTotal }}">
              <td><span class="cfb-serial">{{ $i + 1 }}</span></td>
              <td>
                <div class="cfb-course-name">{{ $course->name }}</div>
                <div class="cfb-course-sub">{{ $course->duration }} months</div>
              </td>
              <td>
                @if($course->courseType)
                  <span class="cfb-type-badge">{{ $course->courseType->name }}</span>
                @else
                  <span class="cfb-no-extra">No type</span>
                @endif
              </td>
              <td>
                <div class="cfb-fee-main">Rs. {{ number_format($course->fee, 2) }}</div>
                <div class="cfb-fee-sub">Base course fee</div>
              </td>
              <td>
                @if($course->feeStructures->isNotEmpty())
                  <div class="cfb-extra-wrap">
                    @foreach($course->feeStructures as $row)
                      <span class="cfb-extra-badge">{{ $row->fee_type_name }} <span style="opacity:.65;margin-left:4px;">₹{{ number_format($row->amount, 0) }}</span></span>
                    @endforeach
                  </div>
                @else
                  <span class="cfb-no-extra">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" style="width:10px;height:10px;"><circle cx="8" cy="8" r="7" fill="none" stroke="currentColor" stroke-width="1.5"/><path d="M5 8h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    None bound
                  </span>
                @endif
              </td>
              <td>
                <div class="cfb-total-main">Rs. {{ number_format($course->fee + $extraTotal, 2) }}</div>
                <div class="cfb-fee-sub">Course + extra fees</div>
              </td>
              <td>
                <a href="{{ route('institute.courses.fee-bindings.edit', $course) }}" class="cfb-manage-btn">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" style="width:12px;height:12px;"><path d="M12.146 1.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-4 1.5a.5.5 0 0 1-.65-.65l1.5-4a.5.5 0 0 1 .11-.168l10-10z"/></svg>
                  Manage Fees
                </a>
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
  const feeFilter   = document.getElementById('fee-range-filter');
  const countEl     = document.getElementById('cfb-visible');
  const rows        = [...document.querySelectorAll('[data-fee-binding-row]')];

  if (!rows.length) return;

  function applyFilters() {
    const term    = (searchInput?.value || '').trim().toLowerCase();
    const range   = feeFilter?.value || '';
    let [minFee, maxFee] = range ? range.split('-').map(Number) : [0, Infinity];
    if (!range) { minFee = 0; maxFee = Infinity; }

    let visible = 0;
    rows.forEach(function (row) {
      const fee       = parseFloat(row.dataset.fee) || 0;
      const matchText = !term || row.textContent.toLowerCase().includes(term);
      const matchFee  = fee >= minFee && fee <= maxFee;
      const show      = matchText && matchFee;
      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    if (countEl) countEl.textContent = visible;
  }

  searchInput?.addEventListener('input', applyFilters);
  feeFilter?.addEventListener('change', applyFilters);
});
</script>
@endpush
