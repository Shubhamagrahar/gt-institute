@extends('layouts.institute')
@section('title','Running Students')
@section('topbar-actions')
  <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-primary btn-sm">+ New Admission</a>
@endsection

@push('styles')
<style>
.gt-tbl-wrap { overflow-x: auto; }
.gt-tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
.gt-tbl thead th {
  background: var(--bg-3); padding: 10px 12px; text-align: left;
  font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px;
  color: var(--text-2); white-space: nowrap; border-bottom: 1px solid var(--border);
}
.gt-tbl tbody td { padding: 10px 12px; border-bottom: 1px solid var(--border); vertical-align: middle; }
.gt-tbl tbody tr:last-child td { border-bottom: none; }
.gt-tbl tbody tr:hover { background: var(--bg-3); }
.gt-col-toggle-btn { position: relative; }
.gt-col-menu {
  position: absolute; right: 0; top: calc(100% + 6px); background: var(--bg-2);
  border: 1px solid var(--border); border-radius: 10px; padding: 8px;
  min-width: 180px; z-index: 50; display: none; box-shadow: 0 8px 24px rgba(0,0,0,.12);
}
.gt-col-menu.open { display: block; }
.gt-col-menu label { display: flex; align-items: center; gap: 8px; padding: 6px 8px;
  font-size: 12.5px; cursor: pointer; border-radius: 6px; user-select: none; }
.gt-col-menu label:hover { background: var(--bg-3); }
.avatar-sm { width: 34px; height: 34px; border-radius: 50%; object-fit: cover;
  background: var(--accent); display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0; overflow: hidden; }
.avatar-sm img { width: 100%; height: 100%; object-fit: cover; }
</style>
@endpush

@section('content')

{{-- Stats bar --}}
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
  <div class="gt-card" style="flex:1;min-width:140px;padding:14px 18px;">
    <div style="font-size:22px;font-weight:900;color:var(--accent)">{{ $enrollments->total() }}</div>
    <div style="font-size:11px;color:var(--text-2);margin-top:2px;">Running Students</div>
  </div>
  <div class="gt-card" style="flex:1;min-width:140px;padding:14px 18px;">
    <div style="font-size:22px;font-weight:900;color:#f59e0b">{{ \App\Models\CourseBook::where('institute_id', Auth::guard('institute')->user()->institute_id)->where('status','OPEN')->count() }}</div>
    <div style="font-size:11px;color:var(--text-2);margin-top:2px;">Pending Admission</div>
  </div>
  <div class="gt-card" style="flex:1;min-width:140px;padding:14px 18px;">
    <div style="font-size:22px;font-weight:900;color:#ef4444">{{ \App\Models\CourseBook::where('institute_id', Auth::guard('institute')->user()->institute_id)->where('status','EXPIRED')->count() }}</div>
    <div style="font-size:11px;color:var(--text-2);margin-top:2px;"><a href="{{ route('institute.students.expired') }}" style="color:inherit">Expired Bookings ↗</a></div>
  </div>
</div>

<div class="gt-card">
  <div class="gt-card-header" style="flex-wrap:wrap;gap:10px;">
    <div class="gt-card-title">Running Students</div>

    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-left:auto;">

      {{-- Filters --}}
      <form id="filter-form" method="GET" action="{{ route('institute.students.index') }}" style="display:contents;">
        <input type="hidden" name="q" id="hidden-q" value="{{ $search }}">

        <select name="session_id" onchange="document.getElementById('filter-form').submit()" class="gt-select" style="font-size:12px;padding:6px 10px;width:auto;">
          <option value="">All Sessions</option>
          @foreach($sessions as $sess)
            <option value="{{ $sess->id }}" {{ $sessionId == $sess->id ? 'selected' : '' }}>
              {{ $sess->name }}{{ $sess->is_active ? ' ●' : '' }}
            </option>
          @endforeach
        </select>

        <select name="course_id" onchange="this.form.submit()" class="gt-select" style="font-size:12px;padding:6px 10px;width:auto;">
          <option value="">All Courses</option>
          @foreach($courses as $c)
            <option value="{{ $c->id }}" {{ $courseId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
          @endforeach
        </select>

        <select name="batch_id" onchange="this.form.submit()" class="gt-select" style="font-size:12px;padding:6px 10px;width:auto;">
          <option value="">All Batches</option>
          @foreach($batches as $b)
            <option value="{{ $b->id }}" {{ $batchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
          @endforeach
        </select>
      </form>

      {{-- Search --}}
      <div style="position:relative;">
        <svg style="position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--text-2);" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="search-input" placeholder="Search name / mobile / enrollment…"
          value="{{ $search }}"
          style="padding:6px 10px 6px 30px;border:1px solid var(--border);border-radius:7px;font-size:12.5px;background:var(--bg-3);color:var(--text);width:240px;outline:none;">
      </div>

      {{-- Column toggle --}}
      <div class="gt-col-toggle-btn">
        <button type="button" id="col-toggle-btn" class="btn btn-outline btn-sm" style="font-size:12px;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg>
          Columns
        </button>
        <div class="gt-col-menu" id="col-menu">
          <label><input type="checkbox" data-col="col-photo" checked> Photo</label>
          <label><input type="checkbox" data-col="col-name" checked> Name</label>
          <label><input type="checkbox" data-col="col-mobile" checked> Mobile</label>
          <label><input type="checkbox" data-col="col-enroll" checked> Enrollment No.</label>
          <label><input type="checkbox" data-col="col-course" checked> Course</label>
          <label><input type="checkbox" data-col="col-batch" checked> Batch</label>
          <label><input type="checkbox" data-col="col-date" checked> Start Date</label>
          <label><input type="checkbox" data-col="col-action" checked> Action</label>
        </div>
      </div>
    </div>
  </div>

  <div class="gt-tbl-wrap">
    <table class="gt-tbl" id="students-table">
      <thead>
        <tr>
          <th class="col-photo">#</th>
          <th class="col-name">Student</th>
          <th class="col-mobile">Mobile</th>
          <th class="col-enroll">Enrollment No.</th>
          <th class="col-course">Course</th>
          <th class="col-batch">Batch</th>
          <th class="col-date">Start Date</th>
          <th class="col-action">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($enrollments as $i => $enr)
          @php $stu = $enr->student; $profile = $stu?->profile; $photo = $profile?->photo; @endphp
          <tr>
            <td class="col-photo">
              <div class="avatar-sm">
                @if($photo && $photo !== 'images/user.svg')
                  <img src="{{ asset($photo) }}" alt="">
                @else
                  {{ strtoupper(substr($profile?->name ?? 'S', 0, 1)) }}
                @endif
              </div>
            </td>
            <td class="col-name">
              <div style="font-weight:600;font-size:13px;">{{ $profile?->name ?? $stu?->user_id }}</div>
              <div style="font-size:11px;color:var(--text-2);">{{ $stu?->user_id }}</div>
            </td>
            <td class="col-mobile" style="color:var(--text-2);">{{ $stu?->mobile ?? '—' }}</td>
            <td class="col-enroll">
              <span style="font-family:monospace;font-size:12px;background:var(--bg-3);padding:2px 8px;border-radius:5px;border:1px solid var(--border);">
                {{ $enr->enrollment_no ?? '—' }}
              </span>
            </td>
            <td class="col-course">{{ $enr->course?->name ?? '—' }}</td>
            <td class="col-batch">{{ $enr->batch?->name ?? '—' }}</td>
            <td class="col-date" style="color:var(--text-2);">{{ $enr->start_date?->format('d M Y') ?? '—' }}</td>
            <td class="col-action">
              <a href="{{ route('institute.students.show', $stu) }}" class="btn btn-outline btn-sm" style="font-size:11px;">View</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-2);">No running students found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($enrollments->hasPages())
  <div style="padding:14px 16px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
    <div style="font-size:12px;color:var(--text-2);">
      Showing {{ $enrollments->firstItem() }}–{{ $enrollments->lastItem() }} of {{ $enrollments->total() }} records
    </div>
    <div style="display:flex;gap:4px;flex-wrap:wrap;">
      @if($enrollments->onFirstPage())
        <span class="btn btn-outline btn-sm" style="opacity:.4;cursor:default;font-size:12px;">← Prev</span>
      @else
        <a href="{{ $enrollments->previousPageUrl() }}" class="btn btn-outline btn-sm" style="font-size:12px;">← Prev</a>
      @endif
      @foreach($enrollments->getUrlRange(max(1,$enrollments->currentPage()-2), min($enrollments->lastPage(),$enrollments->currentPage()+2)) as $page => $url)
        @if($page == $enrollments->currentPage())
          <span class="btn btn-primary btn-sm" style="font-size:12px;">{{ $page }}</span>
        @else
          <a href="{{ $url }}" class="btn btn-outline btn-sm" style="font-size:12px;">{{ $page }}</a>
        @endif
      @endforeach
      @if($enrollments->hasMorePages())
        <a href="{{ $enrollments->nextPageUrl() }}" class="btn btn-outline btn-sm" style="font-size:12px;">Next →</a>
      @else
        <span class="btn btn-outline btn-sm" style="opacity:.4;cursor:default;font-size:12px;">Next →</span>
      @endif
    </div>
  </div>
  @endif
</div>

@endsection

@push('scripts')
<script>
// Debounced search
const searchInput = document.getElementById('search-input');
const hiddenQ     = document.getElementById('hidden-q');
const filterForm  = document.getElementById('filter-form');
let debounceTimer;
searchInput?.addEventListener('keyup', function () {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    hiddenQ.value = this.value.trim();
    filterForm.submit();
  }, 350);
});

// Column toggle
const colBtn  = document.getElementById('col-toggle-btn');
const colMenu = document.getElementById('col-menu');
colBtn?.addEventListener('click', (e) => { e.stopPropagation(); colMenu.classList.toggle('open'); });
document.addEventListener('click', () => colMenu?.classList.remove('open'));

const STORAGE_KEY = 'stu_running_cols';
const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');

colMenu?.querySelectorAll('input[type=checkbox]').forEach(cb => {
  const col = cb.dataset.col;
  if (col in saved) cb.checked = saved[col];
  applyCol(col, cb.checked);
  cb.addEventListener('change', function () {
    applyCol(col, this.checked);
    const state = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
    state[col] = this.checked;
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
  });
});

function applyCol(col, show) {
  document.querySelectorAll('.' + col).forEach(el => {
    el.style.display = show ? '' : 'none';
  });
}
</script>
@endpush
