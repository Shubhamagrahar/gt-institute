@extends('layouts.institute')
@section('title','Attendance Register')
@section('page-title','Attendance Register')

@push('styles')
<style>
/* ═══════════════════════════════════════
   ATTENDANCE REGISTER — student-card UI
═══════════════════════════════════════ */

/* ── Filter card ── */
.reg-card {
  background: var(--bg-2); border: 1px solid var(--border);
  border-radius: 16px; overflow: hidden; margin-bottom: 20px;
}
.reg-card-head {
  padding: 14px 22px; border-bottom: 1px solid var(--border);
  display: flex; align-items: center; gap: 10px;
}
.reg-card-icon {
  width: 32px; height: 32px; border-radius: 8px;
  background: var(--accent-bg, #ede9fe); color: var(--accent);
  display: flex; align-items: center; justify-content: center;
}
.reg-card-icon svg { width: 16px; height: 16px; }
.reg-card-title { font-size: 13px; font-weight: 800; color: var(--text-1); }

.reg-filters {
  display: grid; grid-template-columns: auto 1fr;
  border-bottom: 1px solid var(--border);
}
@media(max-width:640px) { .reg-filters { grid-template-columns: 1fr; } }
.reg-field {
  padding: 18px 22px; border-right: 1px solid var(--border);
  display: flex; flex-direction: column; gap: 8px;
}
.reg-field:last-child { border-right: none; }
.reg-label {
  font-size: 11px; font-weight: 800; color: var(--text-2);
  letter-spacing: .6px; text-transform: uppercase;
  display: flex; align-items: center; gap: 6px;
}
.reg-label svg { width: 13px; height: 13px; opacity: .7; }
.reg-toggle { display: flex; border: 1.5px solid var(--border); border-radius: 10px; overflow: hidden; }
.reg-toggle-btn {
  flex: 1; padding: 9px 18px; font-size: 12px; font-weight: 700;
  cursor: pointer; background: var(--bg-2); color: var(--text-2);
  border: none; transition: .15s; font-family: inherit;
}
.reg-toggle-btn.active { background: var(--accent); color: #fff; }
.reg-select {
  width: 100%; padding: 10px 36px 10px 14px; border-radius: 10px;
  border: 1.5px solid var(--border); background: var(--bg-2);
  color: var(--text-1); font-size: 14px; font-weight: 600;
  appearance: none; font-family: inherit;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%239ca3af' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 12px center;
  transition: border-color .15s, box-shadow .15s;
}
.reg-select:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(124,58,237,.12); }
.reg-load-row {
  padding: 14px 22px; display: flex; align-items: center;
  justify-content: space-between; gap: 12px; flex-wrap: wrap;
}
.reg-load-hint { font-size: 12px; color: var(--text-2); font-weight: 500; }
.reg-load-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 22px; border-radius: 10px; border: none;
  background: var(--accent); color: #fff;
  font-size: 13px; font-weight: 700; cursor: pointer;
  box-shadow: 0 2px 8px rgba(108,93,211,.35); transition: .15s; font-family: inherit;
}
.reg-load-btn:hover { opacity: .88; transform: translateY(-1px); }
.reg-load-btn svg { width: 15px; height: 15px; }

/* ── Actions bar ── */
.reg-actions-bar {
  display: flex; align-items: center; justify-content: space-between;
  gap: 12px; flex-wrap: wrap; margin-bottom: 14px;
}
.reg-count { font-size: 13px; color: var(--text-2); font-weight: 600; }
.reg-count strong { color: var(--text-1); font-weight: 800; }
.reg-export-btn {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 8px 18px; border-radius: 9px;
  border: 1.5px solid var(--border); background: var(--bg-2);
  color: var(--text-1); font-size: 12px; font-weight: 700;
  cursor: pointer; text-decoration: none; transition: .12s; font-family: inherit;
}
.reg-export-btn:hover { border-color: var(--accent); color: var(--accent); }
.reg-export-btn svg { width: 14px; height: 14px; }

/* ── Student card list ── */
.reg-student-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 32px; }
.reg-stu-card {
  background: var(--bg-2); border: 1px solid var(--border);
  border-radius: 14px; overflow: hidden; transition: box-shadow .15s;
}
.reg-stu-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.08); }
.reg-stu-body {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 18px; flex-wrap: wrap;
}
.reg-stu-ava {
  width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
  background: var(--accent); display: flex; align-items: center;
  justify-content: center; font-size: 14px; font-weight: 900;
  color: #fff; overflow: hidden;
}
.reg-stu-ava img { width: 100%; height: 100%; object-fit: cover; }
.reg-stu-info { flex: 1; min-width: 130px; }
.reg-stu-name   { font-size: 14px; font-weight: 800; color: var(--text-1); }
.reg-stu-enroll { font-size: 12px; color: var(--text-2); margin-top: 1px; }
.reg-stu-stats  { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.reg-stat-chip  { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 800; }
.reg-stat-chip.p { background: #dcfce7; color: #15803d; }
.reg-stat-chip.a { background: #fee2e2; color: #b91c1c; }
.reg-stat-chip.l { background: #fef9c3; color: #854d0e; }
.reg-stat-chip.t { background: var(--bg-3); color: var(--text-2); }
.reg-stu-pct { font-size: 22px; font-weight: 900; padding: 0 10px; min-width: 56px; text-align: center; }
.reg-stu-pct.good   { color: #15803d; }
.reg-stu-pct.warn   { color: #d97706; }
.reg-stu-pct.danger { color: #dc2626; }
.reg-stu-pct.na     { color: var(--text-2); font-size: 14px; }
.reg-stu-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; }
.reg-stu-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 14px; border-radius: 9px;
  border: 1.5px solid var(--border); background: var(--bg-2);
  color: var(--text-1); font-size: 12px; font-weight: 700;
  cursor: pointer; transition: .12s; text-decoration: none; font-family: inherit;
}
.reg-stu-btn.primary { background: var(--accent); border-color: var(--accent); color: #fff; box-shadow: 0 2px 8px rgba(108,93,211,.25); }
.reg-stu-btn:hover:not(.primary) { border-color: var(--accent); color: var(--accent); }
.reg-stu-btn svg { width: 13px; height: 13px; }
.reg-stu-bar { height: 3px; background: var(--bg-3); }
.reg-stu-bar-fill { height: 100%; transition: width .4s; }
.reg-stu-bar-fill.good   { background: #22c55e; }
.reg-stu-bar-fill.warn   { background: #f59e0b; }
.reg-stu-bar-fill.danger { background: #ef4444; }

/* ── State placeholder ── */
.reg-state {
  padding: 64px 24px; text-align: center; color: var(--text-2);
  background: var(--bg-2); border: 1px solid var(--border); border-radius: 14px;
}
.reg-state svg { display: block; margin: 0 auto 16px; opacity: .2; }
.reg-state h3  { font-size: 15px; font-weight: 700; color: var(--text-1); margin: 0 0 6px; }
.reg-state p   { font-size: 13px; margin: 0; }
.reg-spinner {
  width: 36px; height: 36px; border: 3px solid var(--border);
  border-top-color: var(--accent); border-radius: 50%;
  animation: _rspin .55s linear infinite; margin: 0 auto 16px;
}
@keyframes _rspin { to { transform: rotate(360deg); } }

/* ═══════════════════════════════════════
   MONTHLY MODAL
═══════════════════════════════════════ */
.mol-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,.45);
  display: flex; align-items: center; justify-content: center;
  z-index: 1050; padding: 16px;
}
.mol-box {
  background: var(--bg-2); border-radius: 18px; width: 100%;
  max-width: 780px; max-height: 92vh; display: flex;
  flex-direction: column; overflow: hidden;
  box-shadow: 0 24px 64px rgba(0,0,0,.25);
}
/* Modal header */
.mol-header {
  display: flex; align-items: center; gap: 14px;
  padding: 18px 22px; border-bottom: 1px solid var(--border); flex-shrink: 0;
}
.mol-header-ava {
  width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
  background: var(--accent); display: flex; align-items: center;
  justify-content: center; font-size: 14px; font-weight: 900;
  color: #fff; overflow: hidden;
}
.mol-header-ava img { width: 100%; height: 100%; object-fit: cover; }
.mol-header-info { flex: 1; min-width: 0; }
.mol-header-name { font-size: 15px; font-weight: 900; color: var(--text-1); }
.mol-header-sub  { font-size: 12px; color: var(--text-2); margin-top: 2px; }
.mol-close-btn {
  width: 34px; height: 34px; border-radius: 50%;
  border: 1.5px solid var(--border); background: var(--bg-2);
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; transition: .12s; font-family: inherit; color: var(--text-1);
}
.mol-close-btn:hover { border-color: var(--accent); color: var(--accent); }
.mol-close-btn svg { width: 14px; height: 14px; }

/* Tabs row */
.mol-tabs-row {
  display: flex; align-items: center; justify-content: space-between;
  gap: 12px; padding: 12px 22px; border-bottom: 1px solid var(--border);
  flex-shrink: 0; flex-wrap: wrap;
}
.mol-tabs-scroll {
  display: flex; gap: 6px; overflow-x: auto; flex: 1;
  scrollbar-width: none; -ms-overflow-style: none;
}
.mol-tabs-scroll::-webkit-scrollbar { display: none; }
.mol-tab {
  flex-shrink: 0; padding: 7px 14px; border-radius: 8px;
  border: 1.5px solid var(--border); background: var(--bg-2);
  font-size: 12px; font-weight: 700; color: var(--text-2);
  cursor: pointer; transition: .12s; white-space: nowrap; font-family: inherit;
}
.mol-tab.active { background: var(--accent); border-color: var(--accent); color: #fff; }
.mol-tab:hover:not(.active) { border-color: var(--accent); color: var(--accent); }
.mol-exp-btn {
  flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 16px; border-radius: 9px;
  border: 1.5px solid var(--border); background: var(--bg-2);
  color: var(--text-1); font-size: 12px; font-weight: 700;
  cursor: pointer; transition: .12s; text-decoration: none; font-family: inherit;
}
.mol-exp-btn:hover { border-color: var(--accent); color: var(--accent); }
.mol-exp-btn svg { width: 13px; height: 13px; }

/* Calendar body */
.mol-body { flex: 1; overflow-y: auto; padding: 20px 22px; }
.mol-cal-title {
  font-size: 15px; font-weight: 900; text-align: center; margin-bottom: 14px;
}
.mol-cal-grid {
  display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px;
}
.mol-dow-label {
  text-align: center; font-size: 10px; font-weight: 800;
  color: var(--text-2); letter-spacing: .5px; padding: 6px 0;
}
.mol-day-cell {
  border: 1.5px solid var(--border); border-radius: 10px;
  padding: 6px 4px; cursor: default;
  display: flex; flex-direction: column; align-items: center;
  gap: 5px; min-height: 62px; background: var(--bg-2);
}
.mol-day-cell.today-cell { border-color: var(--accent); background: rgba(108,93,211,.05); }
.mol-day-num { font-size: 11px; font-weight: 800; color: var(--text-2); line-height: 1; }
.mol-day-cell.today-cell .mol-day-num { color: var(--accent); }
.mol-day-chip {
  width: 32px; height: 32px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 900;
}
.mol-day-chip.P     { background: #16a34a; color: #fff; box-shadow: 0 2px 6px rgba(22,163,74,.3); }
.mol-day-chip.A     { background: #dc2626; color: #fff; box-shadow: 0 2px 6px rgba(220,38,38,.3); }
.mol-day-chip.L     { background: #d97706; color: #fff; box-shadow: 0 2px 6px rgba(217,119,6,.3); }
.mol-day-chip.blank { background: var(--bg-3); color: var(--text-2); font-size: 16px; }

/* Modal footer */
.mol-footer {
  border-top: 1px solid var(--border); padding: 14px 22px;
  flex-shrink: 0; display: flex; align-items: center;
  gap: 10px; flex-wrap: wrap;
}
.mol-sum-chips { display: flex; gap: 8px; flex-wrap: wrap; flex: 1; }
.mol-sum-chip { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; }
.mol-sum-chip.p   { background: #dcfce7; color: #15803d; }
.mol-sum-chip.a   { background: #fee2e2; color: #b91c1c; }
.mol-sum-chip.l   { background: #fef9c3; color: #854d0e; }
.mol-sum-chip.pct { background: var(--bg-3); color: var(--text-1); border: 1.5px solid var(--border); }
.mol-footer-hint { font-size: 11px; color: var(--text-2); font-weight: 500; }
</style>
@endpush

@section('content')

{{-- ══ PAGE HEADER ══════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,var(--accent) 0%,#5b21b6 100%);border-radius:16px;padding:22px 28px;margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
  <div style="width:46px;height:46px;border-radius:12px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
    <svg width="22" height="22" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
      <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
      <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
      <rect x="7" y="14" width="3" height="3" rx=".5"/><rect x="11" y="14" width="3" height="3" rx=".5"/>
    </svg>
  </div>
  <div>
    <div style="font-size:18px;font-weight:900;color:#fff;line-height:1.2;">Attendance Register</div>
    <div style="font-size:12px;color:rgba(255,255,255,.65);margin-top:3px;font-weight:500;">Month-wise records — open any student to view &amp; edit attendance</div>
  </div>
</div>

{{-- ══ FILTER CARD ═══════════════════════════════════════════════ --}}
<div class="reg-card">
  <div class="reg-card-head">
    <div class="reg-card-icon">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="20" y2="12"/><line x1="12" y1="18" x2="20" y2="18"/>
      </svg>
    </div>
    <span class="reg-card-title">Filter Students</span>
  </div>

  <div class="reg-filters">
    {{-- View By --}}
    <div class="reg-field">
      <div class="reg-label">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
          <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
        </svg>
        View By
      </div>
      <div class="reg-toggle">
        <button class="reg-toggle-btn active" id="btn-course" onclick="setView('course')">Course</button>
        <button class="reg-toggle-btn"         id="btn-batch"  onclick="setView('batch')">Batch</button>
      </div>
    </div>

    {{-- Course --}}
    <div class="reg-field" id="grp-course">
      <div class="reg-label">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
          <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
        </svg>
        Course
      </div>
      <select id="reg-course" class="reg-select">
        <option value="">— Select Course —</option>
        @foreach($courses as $c)
          <option value="{{ $c->id }}">{{ $c->name }}{{ $c->course_code ? ' ('.$c->course_code.')' : '' }}</option>
        @endforeach
      </select>
    </div>

    {{-- Batch --}}
    <div class="reg-field" id="grp-batch" style="display:none">
      <div class="reg-label">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
        Batch
      </div>
      <select id="reg-batch" class="reg-select">
        <option value="">— Select Batch —</option>
        @foreach($batches as $b)
          @php $st = $b->start_time ? \Carbon\Carbon::parse('2000-01-01 '.$b->start_time)->format('h:i A') : ''; @endphp
          <option value="{{ $b->id }}">{{ $b->name }}{{ $st ? ' — '.$st : '' }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="reg-load-row">
    <div class="reg-load-hint">Select a course or batch to view all enrolled students with their attendance summary.</div>
    <button class="reg-load-btn" onclick="handleLoad()">
      <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/>
      </svg>
      Load Register
    </button>
  </div>
</div>

{{-- ══ STUDENT LIST ════════════════════════════════════════════ --}}
<div id="reg-wrap">
  <div class="reg-state">
    <svg width="52" height="52" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
      <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
      <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
    </svg>
    <h3>Attendance Register</h3>
    <p>Select a course or batch above, then click Load Register.</p>
  </div>
</div>

{{-- ══ MONTHLY MODAL ═══════════════════════════════════════════ --}}
<div class="mol-overlay" id="mol-overlay" style="display:none" onclick="closeMolOnBg(event)">
  <div class="mol-box" id="mol-box">
    {{-- Header --}}
    <div class="mol-header">
      <div class="mol-header-ava" id="mol-ava"></div>
      <div class="mol-header-info">
        <div class="mol-header-name" id="mol-name">—</div>
        <div class="mol-header-sub"  id="mol-sub">—</div>
      </div>
      <button class="mol-close-btn" onclick="closeMol()">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    {{-- Month tabs + export --}}
    <div class="mol-tabs-row">
      <div class="mol-tabs-scroll" id="mol-tabs"></div>
      <a id="mol-exp-month" href="#" target="_blank" class="mol-exp-btn">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Export This Month
      </a>
    </div>
    {{-- Calendar body --}}
    <div class="mol-body" id="mol-body">
      <div style="text-align:center;padding:40px"><div class="reg-spinner"></div></div>
    </div>
    {{-- Summary footer --}}
    <div class="mol-footer" id="mol-footer" style="display:none">
      <div class="mol-sum-chips" id="mol-sum"></div>
      <div class="mol-footer-hint">· = Not marked &nbsp;·&nbsp; P = Present &nbsp;·&nbsp; A = Absent &nbsp;·&nbsp; L = Late</div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const urls   = {
  students   : '{{ route("institute.attendance.register.students") }}',
  months     : '{{ route("institute.attendance.register.student-months") }}',
  cell       : '{{ route("institute.attendance.register.cell") }}',
  exportAll  : '{{ route("institute.attendance.register.export.all") }}',
  exportStu  : '{{ route("institute.attendance.register.export.student") }}',
  exportMonth: '{{ route("institute.attendance.register.export.month-student") }}',
};

let currentView    = 'course';
let loadedStudents = [];

// Modal state
let molSi       = -1;
let molMonths   = [];
let molMi       = 0;
let molCourseId = null;
let molBatchId  = null;

const todayYMD = new Date().toISOString().slice(0, 10);

// ── Restore URL ──────────────────────────────────────────────────────────────
(function restore() {
  const p = new URLSearchParams(location.search);
  if (p.get('view'))      { currentView = p.get('view'); setView(currentView, false); }
  if (p.get('course_id')) document.getElementById('reg-course').value = p.get('course_id');
  if (p.get('batch_id'))  document.getElementById('reg-batch').value  = p.get('batch_id');
  const ok = (currentView === 'course' && p.get('course_id')) ||
             (currentView === 'batch'  && p.get('batch_id'));
  if (ok) loadStudents();
})();

// ── View toggle ──────────────────────────────────────────────────────────────
function setView(v, save = true) {
  currentView = v;
  document.getElementById('btn-course').classList.toggle('active', v === 'course');
  document.getElementById('btn-batch').classList.toggle('active',  v === 'batch');
  document.getElementById('grp-course').style.display = v === 'course' ? '' : 'none';
  document.getElementById('grp-batch').style.display  = v === 'batch'  ? '' : 'none';
}

// ── Load handler ─────────────────────────────────────────────────────────────
function handleLoad() {
  const cId = document.getElementById('reg-course').value;
  const bId = document.getElementById('reg-batch').value;
  if (currentView === 'course' && !cId) { flash('reg-course'); return; }
  if (currentView === 'batch'  && !bId) { flash('reg-batch');  return; }
  const p = new URLSearchParams({ view: currentView });
  if (cId) p.set('course_id', cId);
  if (bId) p.set('batch_id',  bId);
  history.replaceState(null, '', `?${p}`);
  loadStudents();
}

function flash(id) {
  const el = document.getElementById(id);
  el.style.borderColor = '#ef4444';
  el.addEventListener('change', () => el.style.borderColor = '', { once: true });
}

// ── Fetch student list ───────────────────────────────────────────────────────
async function loadStudents() {
  document.getElementById('reg-wrap').innerHTML =
    `<div class="reg-state"><div class="reg-spinner"></div><h3>Loading…</h3><p>Fetching enrolled students.</p></div>`;

  const cId = document.getElementById('reg-course').value;
  const bId = document.getElementById('reg-batch').value;

  try {
    const u = new URL(urls.students, location.origin);
    u.searchParams.set('view_by', currentView);
    if (cId) u.searchParams.set('course_id', cId);
    if (bId) u.searchParams.set('batch_id',  bId);

    const data = await fetch(u).then(r => r.json());

    if (!data.success) {
      document.getElementById('reg-wrap').innerHTML =
        `<div class="reg-state"><h3>No Students Found</h3><p>${esc(data.message ?? '')}</p></div>`;
      return;
    }
    loadedStudents = data.students;
    renderStudentList(data);
  } catch {
    document.getElementById('reg-wrap').innerHTML =
      `<div class="reg-state"><h3>Error</h3><p>Could not load students. Try again.</p></div>`;
  }
}

function renderStudentList(data) {
  const cId = document.getElementById('reg-course').value;
  const bId = document.getElementById('reg-batch').value;

  const expAllUrl = buildUrl(urls.exportAll, { view_by: currentView,
    ...(cId ? { course_id: cId } : {}), ...(bId ? { batch_id: bId } : {}) });

  const bar = `<div class="reg-actions-bar">
    <div class="reg-count">Showing <strong>${data.students.length}</strong> student${data.students.length !== 1 ? 's' : ''}
      ${data.course_name ? ' &nbsp;·&nbsp; ' + esc(data.course_name) : ''}</div>
    <a href="${expAllUrl}" target="_blank" class="reg-export-btn">
      ${dlSvg()} Export All Students
    </a></div>`;

  const cards = data.students.map((s, si) => {
    const inits  = initials(s.name);
    const ava    = s.photo ? `<img src="${s.photo}" alt="">` : inits;
    const pctCls = pctClass(s.pct);
    const expStu = buildUrl(urls.exportStu, { user_id: s.user_id, ...(s.course_id ? { course_id: s.course_id } : {}) });

    return `<div class="reg-stu-card">
      <div class="reg-stu-body">
        <div class="reg-stu-ava">${ava}</div>
        <div class="reg-stu-info">
          <div class="reg-stu-name">${esc(s.name)}</div>
          <div class="reg-stu-enroll">${esc(s.enrollment_no)}</div>
        </div>
        <div class="reg-stu-stats">
          ${s.present > 0 ? `<span class="reg-stat-chip p">${s.present}P</span>` : ''}
          ${s.absent  > 0 ? `<span class="reg-stat-chip a">${s.absent}A</span>`  : ''}
          ${s.late    > 0 ? `<span class="reg-stat-chip l">${s.late}L</span>`    : ''}
          <span class="reg-stat-chip t">${s.total} days</span>
        </div>
        <div class="reg-stu-pct ${pctCls}">${s.pct !== null ? s.pct + '%' : '—'}</div>
        <div class="reg-stu-actions">
          <button class="reg-stu-btn primary" onclick="openModal(${si})">
            ${calSvg()} View Monthly
          </button>
          <a href="${expStu}" target="_blank" class="reg-stu-btn">
            ${dlSvg()} Export
          </a>
        </div>
      </div>
      <div class="reg-stu-bar">
        <div class="reg-stu-bar-fill ${pctCls}" style="width:${s.pct ?? 0}%"></div>
      </div>
    </div>`;
  }).join('');

  document.getElementById('reg-wrap').innerHTML = bar +
    `<div class="reg-student-list">${cards}</div>`;
}

// ═══════════════════════════════════════
//  MODAL
// ═══════════════════════════════════════

async function openModal(si) {
  const s     = loadedStudents[si];
  molSi       = si;
  molCourseId = s.course_id || null;
  molBatchId  = s.batch_id  || null;

  const inits = initials(s.name);
  const avaEl = document.getElementById('mol-ava');
  avaEl.innerHTML = s.photo ? `<img src="${s.photo}" alt="">` : inits;
  document.getElementById('mol-name').textContent = s.name;
  document.getElementById('mol-sub').textContent  = s.enrollment_no;

  document.getElementById('mol-tabs').innerHTML = '';
  document.getElementById('mol-body').innerHTML = `<div style="text-align:center;padding:40px"><div class="reg-spinner"></div></div>`;
  document.getElementById('mol-footer').style.display = 'none';
  document.getElementById('mol-overlay').style.display = 'flex';
  document.body.style.overflow = 'hidden';

  try {
    const u = new URL(urls.months, location.origin);
    u.searchParams.set('user_id', s.user_id);
    if (s.course_id) u.searchParams.set('course_id', s.course_id);
    if (s.batch_id)  u.searchParams.set('batch_id',  s.batch_id);

    const data = await fetch(u).then(r => r.json());
    if (!data.success) {
      document.getElementById('mol-body').innerHTML =
        `<div style="text-align:center;padding:40px;color:var(--text-2)">${esc(data.message ?? 'No data found.')}</div>`;
      return;
    }
    molMonths = data.months;
    molMi     = molMonths.length - 1; // default: latest month
    renderTabs();
    renderCalendar(molMi);
  } catch {
    document.getElementById('mol-body').innerHTML =
      `<div style="text-align:center;padding:40px;color:var(--text-2)">Failed to load. Please try again.</div>`;
  }
}

function closeMol() {
  document.getElementById('mol-overlay').style.display = 'none';
  document.body.style.overflow = '';
}
function closeMolOnBg(e) { if (e.target === document.getElementById('mol-overlay')) closeMol(); }
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMol(); });

// ── Month tabs ────────────────────────────────────────────────────────────────
function renderTabs() {
  const html = molMonths.map((m, i) =>
    `<button class="mol-tab ${i === molMi ? 'active' : ''}" onclick="switchMonth(${i})">${m.label}</button>`
  ).join('');
  document.getElementById('mol-tabs').innerHTML = html;
  document.getElementById('mol-tabs').children[molMi]
    ?.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
}

function switchMonth(i) { molMi = i; renderTabs(); renderCalendar(i); }

// ── Calendar ─────────────────────────────────────────────────────────────────
const DOW = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

function renderCalendar(mi) {
  const m = molMonths[mi];

  // Export This Month link
  const expUrl = buildUrl(urls.exportMonth, {
    user_id: loadedStudents[molSi].user_id,
    month  : m.key,
    ...(molCourseId ? { course_id: molCourseId } : {}),
  });
  document.getElementById('mol-exp-month').href = expUrl;

  // DOW headers
  let cells = DOW.map(d => `<div class="mol-dow-label">${d}</div>`).join('');
  // Empty offset cells
  for (let i = 0; i < m.first_dow; i++) cells += `<div></div>`;

  // Day cells
  m.days.forEach(day => {
    const st      = day.status;
    const isToday = day.date === todayYMD;
    cells += `<div class="mol-day-cell ${isToday ? 'today-cell' : ''}">
      <div class="mol-day-num">${day.day}</div>
      <div class="mol-day-chip ${st ?? 'blank'}">${st ?? '·'}</div>
    </div>`;
  });

  document.getElementById('mol-body').innerHTML =
    `<div class="mol-cal-title">${m.label}</div>
     <div class="mol-cal-grid">${cells}</div>`;

  refreshSummary(mi);
  document.getElementById('mol-footer').style.display = 'flex';
}

function refreshSummary(mi) {
  const m   = molMonths[mi];
  const pct = m.pct !== null ? m.pct + '%' : 'N/A';
  document.getElementById('mol-sum').innerHTML =
    `<span class="mol-sum-chip p">${m.present} Present</span>
     <span class="mol-sum-chip a">${m.absent} Absent</span>
     ${m.late > 0 ? `<span class="mol-sum-chip l">${m.late} Late</span>` : ''}
     <span class="mol-sum-chip pct">Attendance: ${pct}</span>`;
}

// ── Cycle cell ────────────────────────────────────────────────────────────────
const cycle = { null: 'P', P: 'A', A: 'L', L: null };

async function cycleCell(mi, dayNum) {
  const m   = molMonths[mi];
  const day = m.days.find(d => d.day === dayNum);
  if (!day) return;

  const cur  = day.status;
  const next = cycle[cur];
  const chip = document.getElementById(`mc-${m.key}-${dayNum}`);
  if (chip) { chip.className = `mol-day-chip ${next ?? 'blank'}`; chip.textContent = next ?? '·'; }

  try {
    const res  = await fetch(urls.cell, {
      method : 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body   : JSON.stringify({ att_id: day.att_id ?? null, status: next,
                                user_id: loadedStudents[molSi].user_id,
                                course_id: molCourseId, batch_id: molBatchId, date: day.date }),
    });
    const data = await res.json();
    if (!data.success) throw new Error();
    if (data.att_id) day.att_id = data.att_id;

    // Update cache
    const old = day.status; day.status = next;
    if (old  === 'P') m.present--; else if (old  === 'A') m.absent--; else if (old  === 'L') m.late--;
    if (next === 'P') m.present++; else if (next === 'A') m.absent++; else if (next === 'L') m.late++;
    const mk = m.present + m.absent + m.late;
    m.marked = mk;
    m.pct    = mk > 0 ? Math.round((m.present / mk) * 100) : null;
    refreshSummary(mi);
  } catch {
    if (chip) { chip.className = `mol-day-chip ${cur ?? 'blank'}`; chip.textContent = cur ?? '·'; }
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function buildUrl(base, params) {
  const u = new URL(base, location.origin);
  Object.entries(params).forEach(([k, v]) => { if (v != null) u.searchParams.set(k, v); });
  return u.toString();
}
function initials(name) {
  return String(name ?? '').split(' ').map(w => w[0]).join('').slice(0,2).toUpperCase();
}
function pctClass(pct) {
  return pct === null ? 'na' : pct >= 75 ? 'good' : pct >= 50 ? 'warn' : 'danger';
}
function esc(s) {
  return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function dlSvg() {
  return `<svg fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" viewBox="0 0 24 24">
    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
    <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
  </svg>`;
}
function calSvg() {
  return `<svg fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" viewBox="0 0 24 24">
    <rect x="3" y="4" width="18" height="18" rx="2"/>
    <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
    <line x1="3" y1="10" x2="21" y2="10"/>
  </svg>`;
}
</script>
@endpush
