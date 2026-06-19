@extends('layouts.institute')
@section('title','Student Attendance Report')
@section('page-title','Student Attendance Report')

@push('styles')
<style>
/* ═══════════════════════════════
   STUDENT ATTENDANCE REPORT
═══════════════════════════════ */

/* Search card */
.sr-card { background:var(--bg-2); border:1px solid var(--border); border-radius:16px; overflow:hidden; margin-bottom:20px; }
.sr-card-head { padding:14px 22px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
.sr-card-icon { width:32px; height:32px; border-radius:8px; background:var(--accent-bg,#ede9fe); color:var(--accent); display:flex; align-items:center; justify-content:center; }
.sr-card-icon svg { width:16px; height:16px; }
.sr-card-title { font-size:13px; font-weight:800; color:var(--text-1); }
.sr-search-body { padding:18px 22px; }
.sr-input-row   { position:relative; display:flex; align-items:center; gap:10px; }
.sr-input-icon  { position:absolute; left:14px; pointer-events:none; color:var(--text-2); }
.sr-input-icon svg { width:16px; height:16px; }
.sr-input {
  flex:1; padding:11px 44px; border-radius:10px;
  border:1.5px solid var(--border); background:var(--bg-3);
  color:var(--text-1); font-size:14px; font-weight:500;
  font-family:inherit; transition:border-color .15s,box-shadow .15s;
}
.sr-input:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(124,58,237,.1); background:var(--bg-2); }
.sr-spinner {
  width:20px; height:20px; border:2.5px solid var(--border);
  border-top-color:var(--accent); border-radius:50%;
  animation:_sp .5s linear infinite; display:none; flex-shrink:0;
}
@keyframes _sp { to { transform:rotate(360deg); } }

.sr-results     { margin-top:10px; display:flex; flex-direction:column; gap:5px; }
.sr-result-item { display:flex; align-items:center; gap:12px; padding:10px 14px; border-radius:10px; cursor:pointer; border:1.5px solid var(--border); background:var(--bg-3); transition:.12s; }
.sr-result-item:hover,.sr-result-item.active { border-color:var(--accent); background:var(--bg-2); }
.sr-result-ava  { width:38px; height:38px; border-radius:50%; flex-shrink:0; background:var(--accent); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; color:#fff; overflow:hidden; }
.sr-result-ava img { width:100%; height:100%; object-fit:cover; }
.sr-result-name { font-weight:700; font-size:13px; color:var(--text-1); }
.sr-result-mob  { font-size:12px; color:var(--text-2); margin-top:1px; }
.sr-hint { text-align:center; padding:12px; font-size:12px; color:var(--text-2); font-weight:500; }

/* Student header */
#sr-report { margin-bottom:32px; }
.sr-stu-header {
  background:var(--bg-2); border:1px solid var(--border); border-radius:14px;
  padding:18px 22px; display:flex; align-items:center; gap:16px;
  margin-bottom:16px; flex-wrap:wrap;
}
.sr-stu-ava { width:54px; height:54px; border-radius:50%; flex-shrink:0; background:var(--accent); display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:900; color:#fff; overflow:hidden; }
.sr-stu-ava img { width:100%; height:100%; object-fit:cover; }
.sr-stu-name  { font-size:17px; font-weight:900; color:var(--text-1); }
.sr-stu-mob   { font-size:13px; color:var(--text-2); margin-top:3px; }
.sr-stu-badge { margin-left:auto; padding:6px 14px; border-radius:20px; background:var(--bg-3); border:1px solid var(--border); font-size:12px; font-weight:700; color:var(--text-2); }

/* Course card */
.sr-courses { display:flex; flex-direction:column; gap:16px; }
.sr-course-card { background:var(--bg-2); border:1px solid var(--border); border-radius:14px; overflow:hidden; }

/* Course head */
.sr-chead {
  padding:14px 20px; display:flex; align-items:center; justify-content:space-between;
  gap:12px; flex-wrap:wrap;
}
.sr-cname { font-size:15px; font-weight:800; color:var(--text-1); }
.sr-cmeta { font-size:12px; color:var(--text-2); margin-top:3px; display:flex; gap:8px; flex-wrap:wrap; }
.sr-cmeta-sep { color:var(--border); }
.sr-badge { padding:4px 12px; border-radius:20px; font-size:11px; font-weight:800; flex-shrink:0; }
.sr-badge.RUN   { background:#dcfce7; color:#15803d; }
.sr-badge.CLOSE { background:var(--bg-3); color:var(--text-2); }

/* Summary stats bar (top of table) */
.sr-sum-row {
  display:grid; grid-template-columns:repeat(5,1fr);
  border-top:1px solid var(--border); border-bottom:1px solid var(--border);
  background:var(--bg-3);
}
.sr-sum-cell { padding:12px 8px; text-align:center; border-right:1px solid var(--border); }
.sr-sum-cell:last-child { border-right:none; }
.sr-sum-num   { font-size:22px; font-weight:900; line-height:1; }
.sr-sum-label { font-size:9px; font-weight:800; color:var(--text-2); margin-top:4px; letter-spacing:.5px; text-transform:uppercase; }
.c-green { color:#22c55e; } .c-red { color:#ef4444; } .c-amber { color:#f59e0b; }
.c-purple { color:var(--accent); }
.c-good { color:#15803d; } .c-warn { color:#d97706; } .c-danger { color:#dc2626; } .c-na { color:var(--text-2); }

/* Attendance table */
.sr-table-wrap { overflow-x:auto; }
table.sr-table { border-collapse:collapse; width:100%; min-width:500px; }
.sr-table thead th {
  padding:9px 14px; font-size:10px; font-weight:800; color:var(--text-2);
  letter-spacing:.5px; text-transform:uppercase; text-align:left;
  background:var(--bg-3); border-bottom:2px solid var(--border);
  white-space:nowrap;
}
.sr-table thead th.num { text-align:center; }
.sr-table tbody tr { border-bottom:1px solid var(--border); transition:background .1s; }
.sr-table tbody tr:last-child { border-bottom:none; }
.sr-table tbody tr:hover { background:var(--bg-3); }
.sr-table td { padding:10px 14px; font-size:13px; vertical-align:middle; }
.sr-table td.num { text-align:center; font-weight:700; }
.sr-table td.td-p { color:#15803d; font-weight:800; }
.sr-table td.td-a { color:#b91c1c; font-weight:800; }
.sr-table td.td-l { color:#92400e; font-weight:800; }
.sr-table td.td-t { color:var(--accent); font-weight:800; }

/* Month name cell */
.sr-month-label { font-weight:700; color:var(--text-1); }

/* Mini progress bar in attendance column */
.mini-bar-wrap { display:flex; align-items:center; gap:8px; }
.mini-bar-bg   { flex:1; height:6px; background:var(--bg-3); border-radius:3px; overflow:hidden; min-width:60px; }
.mini-bar      { height:100%; border-radius:3px; }
.mini-bar.good   { background:#22c55e; }
.mini-bar.warn   { background:#f59e0b; }
.mini-bar.danger { background:#ef4444; }
.mini-pct      { font-weight:800; font-size:12px; white-space:nowrap; min-width:38px; }

/* Overall row */
.sr-overall-row td { background:var(--bg-3) !important; font-weight:800; border-top:2px solid var(--border); }
.sr-overall-row:hover td { background:var(--bg-4) !important; }

/* Eligibility + actions footer */
.sr-card-footer {
  padding:14px 20px; display:flex; align-items:center;
  justify-content:space-between; gap:12px; flex-wrap:wrap;
  border-top:1px solid var(--border); background:var(--bg-3);
}
.sr-elig { font-size:12px; font-weight:700; display:flex; align-items:center; gap:6px; }
.sr-elig.ok  { color:#15803d; }
.sr-elig.bad { color:#b91c1c; }
.sr-elig svg { width:14px; height:14px; }
.sr-actions { display:flex; gap:8px; flex-wrap:wrap; }
.sr-btn {
  display:inline-flex; align-items:center; gap:6px;
  padding:8px 16px; border-radius:9px; font-size:12px; font-weight:700;
  cursor:pointer; transition:.12s; text-decoration:none; font-family:inherit;
  border:1.5px solid var(--border); background:var(--bg-2); color:var(--text-1);
}
.sr-btn.primary { background:var(--accent); border-color:var(--accent); color:#fff; box-shadow:0 2px 8px rgba(108,93,211,.25); }
.sr-btn:hover:not(.primary) { border-color:var(--accent); color:var(--accent); }
.sr-btn svg { width:13px; height:13px; }

/* States */
.sr-loading,.sr-empty { padding:64px 24px; text-align:center; color:var(--text-2); background:var(--bg-2); border:1px solid var(--border); border-radius:14px; }
.sr-big-spin { width:36px; height:36px; border:3px solid var(--border); border-top-color:var(--accent); border-radius:50%; animation:_sp .6s linear infinite; margin:0 auto 14px; }
.sr-empty svg { display:block; margin:0 auto 16px; opacity:.15; }
.sr-empty h3  { font-size:15px; font-weight:700; color:var(--text-1); margin:0 0 6px; }
.sr-empty p   { font-size:13px; margin:0; }

@media(max-width:600px) {
  .sr-sum-row { grid-template-columns:repeat(3,1fr); }
  .sr-sum-cell:nth-child(3) { border-right:none; }
  .sr-sum-cell:nth-child(4),.sr-sum-cell:nth-child(5) { display:none; }
}

/* ═══════════════════════════
   MONTHLY MODAL
═══════════════════════════ */
.mol-overlay {
  position:fixed; inset:0; background:rgba(0,0,0,.45);
  display:flex; align-items:center; justify-content:center;
  z-index:1050; padding:16px;
}
.mol-box {
  background:var(--bg-2); border-radius:18px; width:100%;
  max-width:780px; max-height:92vh; display:flex; flex-direction:column;
  overflow:hidden; box-shadow:0 24px 64px rgba(0,0,0,.25);
}
.mol-header { display:flex; align-items:center; gap:14px; padding:18px 22px; border-bottom:1px solid var(--border); flex-shrink:0; }
.mol-ava    { width:40px; height:40px; border-radius:50%; flex-shrink:0; background:var(--accent); display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:900; color:#fff; overflow:hidden; }
.mol-ava img { width:100%; height:100%; object-fit:cover; }
.mol-hinfo  { flex:1; min-width:0; }
.mol-hname  { font-size:15px; font-weight:900; color:var(--text-1); }
.mol-hsub   { font-size:12px; color:var(--text-2); margin-top:2px; }
.mol-xbtn   { width:34px; height:34px; border-radius:50%; border:1.5px solid var(--border); background:var(--bg-3); cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:.12s; color:var(--text-1); }
.mol-xbtn:hover { border-color:var(--accent); color:var(--accent); }
.mol-xbtn svg { width:14px; height:14px; }

.mol-tabs-row { display:flex; align-items:center; gap:12px; padding:12px 22px; border-bottom:1px solid var(--border); flex-shrink:0; flex-wrap:wrap; }
.mol-tabs-scroll { display:flex; gap:6px; overflow-x:auto; flex:1; scrollbar-width:none; }
.mol-tabs-scroll::-webkit-scrollbar { display:none; }
.mol-tab { flex-shrink:0; padding:7px 14px; border-radius:8px; border:1.5px solid var(--border); background:var(--bg-3); font-size:12px; font-weight:700; color:var(--text-2); cursor:pointer; transition:.12s; white-space:nowrap; font-family:inherit; }
.mol-tab.active { background:var(--accent); border-color:var(--accent); color:#fff; }
.mol-tab:hover:not(.active) { border-color:var(--accent); color:var(--accent); }
.mol-exp-btn { flex-shrink:0; display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:9px; border:1.5px solid var(--border); background:var(--bg-3); color:var(--text-1); font-size:12px; font-weight:700; cursor:pointer; transition:.12s; text-decoration:none; font-family:inherit; }
.mol-exp-btn:hover { border-color:var(--accent); color:var(--accent); }
.mol-exp-btn svg { width:13px; height:13px; }

.mol-body { flex:1; overflow-y:auto; padding:20px 22px; }
.mol-cal-title { font-size:15px; font-weight:900; text-align:center; margin-bottom:14px; }
.mol-cal-grid  { display:grid; grid-template-columns:repeat(7,1fr); gap:5px; }
.mol-dow       { text-align:center; font-size:10px; font-weight:800; color:var(--text-2); letter-spacing:.5px; padding:6px 0; }
.mol-day-cell  { border:1.5px solid var(--border); border-radius:10px; padding:6px 4px; cursor:default; display:flex; flex-direction:column; align-items:center; gap:5px; min-height:62px; background:var(--bg-2); }
.mol-day-cell.td { border-color:var(--accent); background:rgba(108,93,211,.05); }
.mol-dnum      { font-size:11px; font-weight:800; color:var(--text-2); }
.mol-day-cell.td .mol-dnum { color:var(--accent); }
.mol-chip      { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:900; }
.mol-chip.P    { background:#16a34a; color:#fff; box-shadow:0 2px 6px rgba(22,163,74,.3); }
.mol-chip.A    { background:#dc2626; color:#fff; box-shadow:0 2px 6px rgba(220,38,38,.3); }
.mol-chip.L    { background:#d97706; color:#fff; box-shadow:0 2px 6px rgba(217,119,6,.3); }
.mol-chip.blank{ background:var(--bg-3); color:var(--text-2); font-size:16px; }

.mol-footer    { border-top:1px solid var(--border); padding:14px 22px; flex-shrink:0; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.mol-sum-chips { display:flex; gap:8px; flex-wrap:wrap; }
.mol-schip     { padding:5px 12px; border-radius:20px; font-size:12px; font-weight:800; }
.mol-schip.p   { background:#dcfce7; color:#15803d; }
.mol-schip.a   { background:#fee2e2; color:#b91c1c; }
.mol-schip.l   { background:#fef9c3; color:#854d0e; }
.mol-schip.t   { background:var(--bg-3); color:var(--text-1); border:1.5px solid var(--border); }
</style>
@endpush

@section('content')

{{-- ══ HEADER ══════════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,var(--accent) 0%,#5b21b6 100%);border-radius:16px;padding:22px 28px;margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
  <div style="width:46px;height:46px;border-radius:12px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
    <svg width="22" height="22" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
  </div>
  <div>
    <div style="font-size:18px;font-weight:900;color:#fff;line-height:1.2;">Student Attendance Report</div>
    <div style="font-size:12px;color:rgba(255,255,255,.65);margin-top:3px;font-weight:500;">Search a student to view course-wise attendance with monthly breakdown</div>
  </div>
</div>

{{-- ══ SEARCH ══════════════════════════════════════════════════ --}}
<div class="sr-card">
  <div class="sr-card-head">
    <div class="sr-card-icon">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
    </div>
    <span class="sr-card-title">Search Student</span>
  </div>
  <div class="sr-search-body">
    <div class="sr-input-row">
      <div class="sr-input-icon">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
      </div>
      <input type="text" id="sr-input" class="sr-input"
             placeholder="Type name or mobile number…"
             autocomplete="off" onkeyup="debouncedSearch()">
      <div class="sr-spinner" id="sr-spin"></div>
    </div>
    <div id="sr-results-wrap"></div>
  </div>
</div>

{{-- ══ REPORT ═══════════════════════════════════════════════════ --}}
<div id="sr-report">
  <div class="sr-empty">
    <svg width="52" height="52" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
    <h3>No Student Selected</h3>
    <p>Search and select a student above to view their attendance report.</p>
  </div>
</div>

{{-- ══ MONTHLY MODAL ═══════════════════════════════════════════ --}}
<div class="mol-overlay" id="mol-overlay" style="display:none" onclick="closeMolBg(event)">
  <div class="mol-box">
    <div class="mol-header">
      <div class="mol-ava" id="mol-ava"></div>
      <div class="mol-hinfo">
        <div class="mol-hname" id="mol-name">—</div>
        <div class="mol-hsub"  id="mol-sub">—</div>
      </div>
      <button class="mol-xbtn" onclick="closeMol()">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
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
    <div class="mol-body" id="mol-body">
      <div style="text-align:center;padding:40px"><div class="sr-big-spin" style="margin:0 auto 14px"></div></div>
    </div>
    <div class="mol-footer" id="mol-footer" style="display:none">
      <div class="mol-sum-chips" id="mol-sum"></div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const urls = {
  search     : '{{ route("institute.attendance.student-report.search") }}',
  load       : '{{ route("institute.attendance.student-report.load") }}',
  months     : '{{ route("institute.attendance.register.student-months") }}',
  exportMonth: '{{ route("institute.attendance.register.export.month-student") }}',
  exportStu  : '{{ route("institute.attendance.register.export.student") }}',
};

const todayYMD = new Date().toISOString().slice(0,10);
let _st = null, activeId = null;
let currentStudent = null, currentCourses = [];
let molMonths = [], molMi = 0, molCourseId = null;

// ── Search ────────────────────────────────────────────────────────────────────
function debouncedSearch() { clearTimeout(_st); _st = setTimeout(doSearch, 300); }

async function doSearch() {
  const q    = document.getElementById('sr-input').value.trim();
  const wrap = document.getElementById('sr-results-wrap');
  const spin = document.getElementById('sr-spin');
  if (q.length < 2) { wrap.innerHTML = q ? `<div class="sr-hint">Type at least 2 characters…</div>` : ''; return; }
  spin.style.display = 'block';
  try {
    const data = await fetch(`${urls.search}?q=${encodeURIComponent(q)}`).then(r => r.json());
    spin.style.display = 'none';
    if (!data.students?.length) { wrap.innerHTML = `<div class="sr-hint">No students found for "<strong>${esc(q)}</strong>".</div>`; return; }
    wrap.innerHTML = `<div class="sr-results">${data.students.map(s => {
      const av = s.photo ? `<img src="${s.photo}" alt="">` : ini(s.name);
      return `<div class="sr-result-item ${s.id===activeId?'active':''}" onclick="loadReport(${s.id},this,'${esc(s.name)}','${s.photo??''}')">
        <div class="sr-result-ava">${av}</div>
        <div><div class="sr-result-name">${esc(s.name)}</div><div class="sr-result-mob">${esc(s.mobile)}</div></div>
        <svg style="margin-left:auto;color:var(--text-2)" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      </div>`;
    }).join('')}</div>`;
  } catch { spin.style.display='none'; wrap.innerHTML=`<div class="sr-hint">Search failed.</div>`; }
}

// ── Load report ───────────────────────────────────────────────────────────────
async function loadReport(userId, el, name, photo) {
  activeId = userId;
  currentStudent = { id: userId, name, photo };
  document.querySelectorAll('.sr-result-item').forEach(i => i.classList.remove('active'));
  el?.classList.add('active');
  document.getElementById('sr-report').innerHTML = `<div class="sr-loading"><div class="sr-big-spin"></div>Loading report…</div>`;
  try {
    const data = await fetch(`${urls.load}?user_id=${userId}`).then(r => r.json());
    if (!data.success) throw new Error();
    currentStudent = { id: userId, name: data.name, photo: data.photo };
    currentCourses = data.courses;
    renderReport(data);
  } catch {
    document.getElementById('sr-report').innerHTML = `<div class="sr-empty"><h3>Error</h3><p>Could not load report.</p></div>`;
  }
}

// ── Render report ─────────────────────────────────────────────────────────────
function renderReport(data) {
  const av = data.photo ? `<img src="${data.photo}" alt="">` : ini(data.name);
  const n  = data.courses?.length ?? 0;

  const header = `<div class="sr-stu-header">
    <div class="sr-stu-ava">${av}</div>
    <div>
      <div class="sr-stu-name">${esc(data.name)}</div>
      ${data.mobile ? `<div class="sr-stu-mob">${esc(data.mobile)}</div>` : ''}
    </div>
    <span class="sr-stu-badge">${n} Course${n!==1?'s':''}</span>
  </div>`;

  if (!n) {
    document.getElementById('sr-report').innerHTML = header +
      `<div class="sr-empty"><h3>No Enrollments</h3><p>This student has no active or completed course enrollments.</p></div>`;
    return;
  }

  const cards = data.courses.map((c, ci) => courseCard(c, ci)).join('');
  document.getElementById('sr-report').innerHTML = header + `<div class="sr-courses">${cards}</div>`;
}

function pctClass(pct) { return pct===null?'na':pct>=75?'good':pct>=50?'warn':'danger'; }

function courseCard(c, ci) {
  const cls    = pctClass(c.pct);
  const pctTxt = c.pct !== null ? c.pct + '%' : 'N/A';

  // Summary stats bar
  const sumBar = `<div class="sr-sum-row">
    <div class="sr-sum-cell"><div class="sr-sum-num c-green">${c.present}</div><div class="sr-sum-label">Present</div></div>
    <div class="sr-sum-cell"><div class="sr-sum-num c-red">${c.absent}</div><div class="sr-sum-label">Absent</div></div>
    <div class="sr-sum-cell"><div class="sr-sum-num c-amber">${c.late}</div><div class="sr-sum-label">Late</div></div>
    <div class="sr-sum-cell"><div class="sr-sum-num c-purple">${c.total}</div><div class="sr-sum-label">Total Days</div></div>
    <div class="sr-sum-cell">
      <div class="sr-sum-num c-${cls}">${pctTxt}</div>
      <div class="sr-sum-label">Attendance</div>
    </div>
  </div>`;

  // Month table rows
  let tableRows = '';
  let totP = 0, totA = 0, totL = 0, totT = 0;

  if (c.by_month?.length) {
    tableRows = c.by_month.map(m => {
      const mp  = pctClass(m.total > 0 ? Math.round((m.present/m.total)*100) : null);
      const mpct = m.total > 0 ? Math.round((m.present/m.total)*100) : null;
      totP += m.present; totA += m.absent; totL += m.late; totT += m.total;
      return `<tr>
        <td><span class="sr-month-label">${esc(m.label)}</span></td>
        <td class="num td-p">${m.present}</td>
        <td class="num td-a">${m.absent}</td>
        <td class="num td-l">${m.late}</td>
        <td class="num td-t">${m.total}</td>
        <td style="min-width:140px">
          <div class="mini-bar-wrap">
            <div class="mini-bar-bg"><div class="mini-bar ${mp}" style="width:${mpct??0}%"></div></div>
            <span class="mini-pct c-${mp}">${mpct !== null ? mpct+'%' : 'N/A'}</span>
          </div>
        </td>
      </tr>`;
    }).join('');

    // Overall row
    const op  = totT > 0 ? Math.round((totP/totT)*100) : null;
    const opc = pctClass(op);
    tableRows += `<tr class="sr-overall-row">
      <td><strong>Overall Total</strong></td>
      <td class="num td-p"><strong>${totP}</strong></td>
      <td class="num td-a"><strong>${totA}</strong></td>
      <td class="num td-l"><strong>${totL}</strong></td>
      <td class="num td-t"><strong>${totT}</strong></td>
      <td>
        <div class="mini-bar-wrap">
          <div class="mini-bar-bg"><div class="mini-bar ${opc}" style="width:${op??0}%"></div></div>
          <span class="mini-pct c-${opc}"><strong>${op!==null?op+'%':'N/A'}</strong></span>
        </div>
      </td>
    </tr>`;
  }

  const table = c.by_month?.length
    ? `<div class="sr-table-wrap">
        <table class="sr-table">
          <thead>
            <tr>
              <th>Month</th>
              <th class="num">Present</th>
              <th class="num">Absent</th>
              <th class="num">Late</th>
              <th class="num">Total</th>
              <th>Attendance %</th>
            </tr>
          </thead>
          <tbody>${tableRows}</tbody>
        </table>
      </div>`
    : `<div style="padding:20px;text-align:center;color:var(--text-2);font-size:13px">No attendance data recorded yet.</div>`;

  // Eligibility
  const elig = c.pct !== null
    ? (c.pct >= 75
        ? `<div class="sr-elig ok"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Eligible for certificate (≥75%)</div>`
        : `<div class="sr-elig bad"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Needs ${75-c.pct}% more for certificate eligibility</div>`)
    : `<div class="sr-elig" style="color:var(--text-2)">No attendance data yet</div>`;

  const expUrl = buildUrl(urls.exportStu, { user_id: currentStudent.id, course_id: c.course_id });
  const startNote = c.start_date ? `<span class="sr-cmeta-sep">·</span> <span>Started ${esc(c.start_date)}</span>` : '';

  return `<div class="sr-course-card">
    <div class="sr-chead">
      <div>
        <div class="sr-cname">${esc(c.course_name)}</div>
        <div class="sr-cmeta">
          <span>Batch: ${esc(c.batch_name)}</span>
          <span class="sr-cmeta-sep">·</span>
          <span>${esc(c.enrollment_no)}</span>
          ${startNote}
        </div>
      </div>
      <span class="sr-badge ${c.status}">${c.status==='RUN'?'Running':'Completed'}</span>
    </div>

    ${sumBar}
    ${table}

    <div class="sr-card-footer">
      ${elig}
      <div class="sr-actions">
        <button class="sr-btn primary" onclick="openMonthModal(${ci})">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          View Monthly Calendar
        </button>
        <a href="${expUrl}" target="_blank" class="sr-btn">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Export All Months
        </a>
      </div>
    </div>
  </div>`;
}

// ═══════════════════════════════════
//  MONTHLY MODAL
// ═══════════════════════════════════
async function openMonthModal(ci) {
  const c = currentCourses[ci];
  molCourseId = c.course_id;
  const av = currentStudent.photo ? `<img src="${currentStudent.photo}" alt="">` : ini(currentStudent.name);
  document.getElementById('mol-ava').innerHTML = av;
  document.getElementById('mol-name').textContent = currentStudent.name;
  document.getElementById('mol-sub').textContent  = c.course_name + ' · ' + c.batch_name;
  document.getElementById('mol-tabs').innerHTML = '';
  document.getElementById('mol-body').innerHTML = `<div style="text-align:center;padding:40px"><div class="sr-big-spin" style="margin:0 auto 14px"></div></div>`;
  document.getElementById('mol-footer').style.display = 'none';
  document.getElementById('mol-overlay').style.display = 'flex';
  document.body.style.overflow = 'hidden';
  try {
    const data = await fetch(buildUrl(urls.months, { user_id: currentStudent.id, course_id: c.course_id })).then(r => r.json());
    if (!data.success) throw new Error(data.message ?? '');
    molMonths = data.months;
    molMi     = molMonths.length - 1;
    renderTabs(); renderCal(molMi);
  } catch(e) {
    document.getElementById('mol-body').innerHTML = `<div style="text-align:center;padding:40px;color:var(--text-2)">${esc(e.message||'Failed to load.')}</div>`;
  }
}

function closeMol() { document.getElementById('mol-overlay').style.display='none'; document.body.style.overflow=''; }
function closeMolBg(e) { if(e.target===document.getElementById('mol-overlay')) closeMol(); }
document.addEventListener('keydown', e => { if(e.key==='Escape') closeMol(); });

function renderTabs() {
  document.getElementById('mol-tabs').innerHTML = molMonths.map((m,i) =>
    `<button class="mol-tab ${i===molMi?'active':''}" onclick="switchMonth(${i})">${m.label}</button>`
  ).join('');
  document.getElementById('mol-tabs').children[molMi]?.scrollIntoView({behavior:'smooth',inline:'center',block:'nearest'});
}
function switchMonth(i) { molMi=i; renderTabs(); renderCal(i); }

const DOW = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
function renderCal(mi) {
  const m = molMonths[mi];
  document.getElementById('mol-exp-month').href = buildUrl(urls.exportMonth, { user_id: currentStudent.id, course_id: molCourseId, month: m.key });
  let cells = DOW.map(d => `<div class="mol-dow">${d}</div>`).join('');
  for(let i=0;i<m.first_dow;i++) cells += `<div></div>`;
  m.days.forEach(day => {
    const st = day.status, isT = day.date===todayYMD;
    cells += `<div class="mol-day-cell ${isT?'td':''}">
      <div class="mol-dnum">${day.day}</div>
      <div class="mol-chip ${st??'blank'}">${st??'·'}</div>
    </div>`;
  });
  document.getElementById('mol-body').innerHTML = `<div class="mol-cal-title">${m.label}</div><div class="mol-cal-grid">${cells}</div>`;
  const pct = m.pct!==null ? m.pct+'%' : 'N/A';
  document.getElementById('mol-sum').innerHTML =
    `<span class="mol-schip p">${m.present} Present</span>
     <span class="mol-schip a">${m.absent} Absent</span>
     ${m.late>0?`<span class="mol-schip l">${m.late} Late</span>`:''}
     <span class="mol-schip t">Attendance: ${pct}</span>`;
  document.getElementById('mol-footer').style.display = 'flex';
}

// Helpers
function buildUrl(base,p) { const u=new URL(base,location.origin); Object.entries(p).forEach(([k,v])=>{if(v!=null)u.searchParams.set(k,v);}); return u.toString(); }
function ini(name) { return String(name??'').split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase(); }
function esc(s)    { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
</script>
@endpush
