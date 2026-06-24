@extends('layouts.institute')
@section('title','Running Enrollments')
@section('topbar-actions')
  <a href="{{ route('institute.enrollment.choose') }}" class="btn btn-primary btn-sm">+ New Admission</a>
@endsection

@push('styles')
<style>
/* Table */
.enr-table-wrap { overflow-x: auto; }
.enr-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.enr-table thead th {
  background: var(--bg-3); padding: 10px 12px; text-align: left;
  font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
  color: var(--text-2); white-space: nowrap; border-bottom: 2px solid var(--border);
  cursor: pointer; user-select: none;
}
.enr-table thead th:hover { color: var(--text); }
.enr-table tbody td { padding: 10px 12px; border-bottom: 1px solid var(--border); vertical-align: middle; }
.enr-table tbody tr:last-child td { border-bottom: none; }
.enr-table tbody tr:hover { background: var(--bg-3); }
.enr-table tbody tr.hidden-row { display: none; }

/* Column toggle */
.col-toggle-wrap { position: relative; }
.col-menu {
  position: absolute; right: 0; top: calc(100% + 6px);
  background: var(--bg-2); border: 1px solid var(--border); border-radius: 12px;
  padding: 8px; min-width: 190px; z-index: 100; display: none;
  box-shadow: 0 12px 32px rgba(0,0,0,.14);
}
.col-menu.open { display: block; }
.col-menu label {
  display: flex; align-items: center; gap: 9px; padding: 6px 10px;
  font-size: 12.5px; cursor: pointer; border-radius: 7px; user-select: none;
}
.col-menu label:hover { background: var(--bg-3); }

/* Avatar */
.avatar-sm {
  width: 34px; height: 34px; border-radius: 50%; object-fit: cover;
  background: var(--accent); display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0; overflow: hidden;
}
.avatar-sm img { width:100%; height:100%; object-fit:cover; }

/* Action buttons in row */
.row-actions { display: flex; gap: 5px; flex-wrap: nowrap; }

/* Search highlight */
.hl { background: #fef08a; border-radius: 2px; padding: 0 1px; }

/* No-result */
.no-match-row { display: none; }
.no-match-row.visible { display: table-row; }
</style>
@endpush

@section('content')

{{-- Stats --}}
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
  <div class="gt-card" style="flex:1;min-width:130px;padding:14px 18px;">
    <div style="font-size:24px;font-weight:900;color:var(--accent)">{{ $enrollments->total() }}</div>
    <div style="font-size:11px;color:var(--text-2);margin-top:2px;">Running Enrollments</div>
  </div>
  <div class="gt-card" style="flex:1;min-width:130px;padding:14px 18px;">
    <div style="font-size:24px;font-weight:900;color:#f59e0b">
      {{ \App\Models\CourseBook::where('institute_id', auth('institute')->user()->institute_id)->where('status','OPEN')->count() }}
    </div>
    <div style="font-size:11px;color:var(--text-2);margin-top:2px;">
      <a href="{{ route('institute.enrollment.pending') }}" style="color:inherit">Pending Admission ↗</a>
    </div>
  </div>
  <div class="gt-card" style="flex:1;min-width:130px;padding:14px 18px;">
    <div style="font-size:24px;font-weight:900;color:#ef4444">
      {{ \App\Models\CourseBook::where('institute_id', auth('institute')->user()->institute_id)->where('status','EXPIRED')->count() }}
    </div>
    <div style="font-size:11px;color:var(--text-2);margin-top:2px;">
      <a href="{{ route('institute.students.expired') }}" style="color:inherit">Expired Bookings ↗</a>
    </div>
  </div>
</div>

<div class="gt-card" style="overflow:visible">

  {{-- Toolbar --}}
  <div style="padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;flex-wrap:wrap;">

    {{-- Search --}}
    <div style="position:relative;flex:1;min-width:200px;max-width:300px;">
      <svg style="position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--text-2);"
           width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" id="enr-search" placeholder="Search name, mobile, enrollment…"
             value="{{ $search }}"
             style="width:100%;padding:7px 10px 7px 32px;border:1px solid var(--border);border-radius:8px;
                    font-size:13px;background:var(--bg-3);color:var(--text);outline:none;">
    </div>

    {{-- Course filter --}}
    <form id="filter-form" method="GET" action="{{ route('institute.students.index') }}" style="display:contents;">
      <input type="hidden" name="q" id="hidden-q" value="{{ $search }}">
      <select name="course_id" onchange="this.form.submit()" class="gt-select"
              style="font-size:13px;padding:7px 10px;width:auto;min-width:130px;">
        <option value="">All Courses</option>
        @foreach($courses as $c)
          <option value="{{ $c->id }}" {{ $courseId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
      </select>

      <select name="batch_id" onchange="this.form.submit()" class="gt-select"
              style="font-size:13px;padding:7px 10px;width:auto;min-width:130px;">
        <option value="">All Batches</option>
        @foreach($batches as $b)
          <option value="{{ $b->id }}" {{ $batchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
        @endforeach
      </select>
    </form>

    {{-- Column visibility --}}
    <div class="col-toggle-wrap" style="margin-left:auto;">
      <button type="button" id="col-toggle-btn" class="btn btn-outline btn-sm">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 9h18M3 15h18M9 3v18M15 3v18"/>
        </svg>
        Columns
      </button>
      <div class="col-menu" id="col-menu">
        <div style="font-size:11px;font-weight:700;color:var(--text-2);padding:4px 10px 6px;border-bottom:1px solid var(--border);margin-bottom:4px;">
          VISIBLE COLUMNS
        </div>
        <label><input type="checkbox" class="col-cb" data-col="c-photo" checked> Photo</label>
        <label><input type="checkbox" class="col-cb" data-col="c-name" checked> Name</label>
        <label><input type="checkbox" class="col-cb" data-col="c-mobile" checked> Mobile</label>
        <label><input type="checkbox" class="col-cb" data-col="c-father"> Father's Name</label>
        <label><input type="checkbox" class="col-cb" data-col="c-enroll" checked> Enrollment No.</label>
        <label><input type="checkbox" class="col-cb" data-col="c-course" checked> Course</label>
        <label><input type="checkbox" class="col-cb" data-col="c-batch" checked> Batch</label>
        <label><input type="checkbox" class="col-cb" data-col="c-gender"> Gender</label>
        <label><input type="checkbox" class="col-cb" data-col="c-dob"> Date of Birth</label>
        <label><input type="checkbox" class="col-cb" data-col="c-date"> Start Date</label>
        <label><input type="checkbox" class="col-cb" data-col="c-action" checked> Actions</label>
      </div>
    </div>

  </div>

  {{-- Table --}}
  <div class="enr-table-wrap">
    <table class="enr-table" id="enr-table">
      <thead>
        <tr>
          <th class="c-photo">#</th>
          <th class="c-name">Student</th>
          <th class="c-mobile">Mobile</th>
          <th class="c-father">Father</th>
          <th class="c-enroll">Enrollment No.</th>
          <th class="c-course">Course</th>
          <th class="c-batch">Batch</th>
          <th class="c-gender">Gender</th>
          <th class="c-dob">Date of Birth</th>
          <th class="c-date">Start Date</th>
          <th class="c-action">Actions</th>
        </tr>
      </thead>
      <tbody id="enr-tbody">
        @forelse($enrollments as $enr)
          @php
            $stu     = $enr->student;
            $profile = $stu?->profile;
            $photo   = $profile?->photo;
            $name    = $profile?->name ?? $stu?->user_id;
            // searchable text used by JS client-side filter
            $searchText = strtolower(implode(' ', array_filter([
              $name, $stu?->mobile, $stu?->user_id, $enr->enrollment_no,
              $profile?->father_name, $enr->course?->name, $enr->batch?->name,
            ])));
          @endphp
          <tr data-search="{{ $searchText }}">
            <td class="c-photo">
              <div class="avatar-sm">
                @if($photo && $photo !== 'images/user.svg')
                  <img src="{{ asset($photo) }}" alt="">
                @else
                  {{ strtoupper(substr($name, 0, 1)) }}
                @endif
              </div>
            </td>
            <td class="c-name">
              <div style="font-weight:700;font-size:13px;" class="searchable">{{ $name }}</div>
              <div style="font-size:11px;color:var(--text-2);" class="searchable">{{ $stu?->user_id }}</div>
            </td>
            <td class="c-mobile" style="color:var(--text-2);white-space:nowrap;" class="searchable">
              {{ $stu?->mobile ?? '—' }}
            </td>
            <td class="c-father" style="color:var(--text-2);">{{ $profile?->father_name ?? '—' }}</td>
            <td class="c-enroll">
              <span style="font-family:monospace;font-size:12px;background:var(--bg-3);padding:2px 8px;border-radius:5px;border:1px solid var(--border);">
                {{ $enr->enrollment_no ?? '—' }}
              </span>
            </td>
            <td class="c-course" style="font-size:12.5px;">{{ $enr->course?->name ?? '—' }}</td>
            <td class="c-batch" style="font-size:12.5px;">{{ $enr->batch?->name ?? '—' }}</td>
            <td class="c-gender" style="color:var(--text-2);font-size:12px;">{{ $profile?->gender ?? '—' }}</td>
            <td class="c-dob" style="color:var(--text-2);font-size:12px;white-space:nowrap;">
              {{ $profile?->dob?->format('d M Y') ?? '—' }}
            </td>
            <td class="c-date" style="color:var(--text-2);font-size:12px;white-space:nowrap;">
              {{ $enr->start_date?->format('d M Y') ?? '—' }}
            </td>
            <td class="c-action">
              <div class="row-actions">
                {{-- View Profile --}}
                <a href="{{ route('institute.students.show', $stu) }}"
                   title="View / Edit Profile"
                   class="btn btn-outline btn-xs"
                   style="display:inline-flex;align-items:center;gap:4px;white-space:nowrap;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                  </svg>
                  Profile
                </a>
                {{-- Collect Fee --}}
                <a href="{{ route('institute.enrollment.fee', $enr) }}"
                   title="Collect Fee"
                   class="btn btn-outline btn-xs"
                   style="display:inline-flex;align-items:center;gap:4px;white-space:nowrap;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                  </svg>
                  Fee
                </a>
                {{-- Application --}}
                <a href="{{ route('institute.enrollment.preview', $enr) }}"
                   title="Download Application"
                   class="btn btn-outline btn-xs"
                   style="display:inline-flex;align-items:center;gap:4px;white-space:nowrap;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="18" x2="12" y2="12"/>
                    <line x1="9" y1="15" x2="15" y2="15"/>
                  </svg>
                  Form
                </a>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="11" style="text-align:center;padding:48px;color:var(--text-2);">
            No running enrollments found.
          </td></tr>
        @endforelse

        {{-- No match row (shown by JS when search has no results) --}}
        <tr class="no-match-row" id="no-match-row">
          <td colspan="11" style="text-align:center;padding:40px;color:var(--text-2);">
            No students match your search.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Pagination + count --}}
  @if($enrollments->hasPages())
  <div style="padding:12px 16px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
    <div style="font-size:12px;color:var(--text-2);">
      Showing {{ $enrollments->firstItem() }}–{{ $enrollments->lastItem() }} of {{ $enrollments->total() }}
    </div>
    <div style="display:flex;gap:4px;flex-wrap:wrap;">
      @if($enrollments->onFirstPage())
        <span class="btn btn-outline btn-sm" style="opacity:.4;cursor:default;">← Prev</span>
      @else
        <a href="{{ $enrollments->previousPageUrl() }}" class="btn btn-outline btn-sm">← Prev</a>
      @endif
      @foreach($enrollments->getUrlRange(max(1,$enrollments->currentPage()-2), min($enrollments->lastPage(),$enrollments->currentPage()+2)) as $page => $url)
        @if($page == $enrollments->currentPage())
          <span class="btn btn-primary btn-sm">{{ $page }}</span>
        @else
          <a href="{{ $url }}" class="btn btn-outline btn-sm">{{ $page }}</a>
        @endif
      @endforeach
      @if($enrollments->hasMorePages())
        <a href="{{ $enrollments->nextPageUrl() }}" class="btn btn-outline btn-sm">Next →</a>
      @else
        <span class="btn btn-outline btn-sm" style="opacity:.4;cursor:default;">Next →</span>
      @endif
    </div>
  </div>
  @endif

</div>
@endsection

@push('scripts')
<script>
(() => {
  // ── Column visibility ──
  const colBtn  = document.getElementById('col-toggle-btn');
  const colMenu = document.getElementById('col-menu');

  colBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    colMenu.classList.toggle('open');
  });
  document.addEventListener('click', () => colMenu?.classList.remove('open'));
  colMenu?.addEventListener('click', (e) => e.stopPropagation());

  document.querySelectorAll('.col-cb').forEach(cb => {
    // restore from localStorage
    const stored = localStorage.getItem('enr_col_' + cb.dataset.col);
    if (stored !== null) cb.checked = stored === '1';
    applyCol(cb);

    cb.addEventListener('change', () => {
      applyCol(cb);
      localStorage.setItem('enr_col_' + cb.dataset.col, cb.checked ? '1' : '0');
    });
  });

  function applyCol(cb) {
    const cls = cb.dataset.col;
    const show = cb.checked;
    document.querySelectorAll('.' + cls).forEach(el => {
      el.style.display = show ? '' : 'none';
    });
  }

  // ── Live search (client-side for current page) ──
  const searchInput = document.getElementById('enr-search');
  const hiddenQ     = document.getElementById('hidden-q');
  const filterForm  = document.getElementById('filter-form');
  const rows        = document.querySelectorAll('#enr-tbody tr[data-search]');
  const noMatch     = document.getElementById('no-match-row');
  let serverTimer;

  searchInput?.addEventListener('keyup', function () {
    const q = this.value.trim().toLowerCase();

    // Client-side filter (instant, this page)
    let visible = 0;
    rows.forEach(row => {
      const text = row.dataset.search || '';
      const match = !q || text.includes(q);
      row.classList.toggle('hidden-row', !match);
      if (match) visible++;
    });
    noMatch?.classList.toggle('visible', visible === 0 && q.length > 0);

    // Server-side search after 400ms (for other pages)
    clearTimeout(serverTimer);
    if (q.length === 0 && !hiddenQ.value) return;
    serverTimer = setTimeout(() => {
      hiddenQ.value = this.value.trim();
      filterForm.submit();
    }, 400);
  });

  // Clear button on Escape
  searchInput?.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      searchInput.value = '';
      searchInput.dispatchEvent(new Event('keyup'));
    }
  });

})();
</script>
@endpush
