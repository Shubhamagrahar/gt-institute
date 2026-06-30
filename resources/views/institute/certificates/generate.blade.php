@extends('layouts.institute')
@section('title','Generate Certificate')
@section('page-title','Generate Certificate / Marksheet')

@push('styles')
<style>
.gen-layout { display:grid; grid-template-columns:380px 1fr; gap:20px; align-items:start; }
@media(max-width:900px){ .gen-layout{grid-template-columns:1fr;} }

/* Search panel */
.search-panel { border-radius:14px; border:1.5px solid var(--border); background:var(--bg-2); overflow:hidden; position:sticky; top:20px; }
.panel-head { padding:16px 18px; border-bottom:1px solid var(--border); font-size:14px; font-weight:800; display:flex; align-items:center; gap:8px; }
.panel-body { padding:18px; }

.search-wrap { position:relative; margin-bottom:14px; }
.search-input { width:100%; padding:11px 14px 11px 38px; border:1.5px solid var(--border); border-radius:10px; font-size:14px; background:var(--bg-3); color:var(--text); outline:none; transition:.15s; box-sizing:border-box; }
.search-input:focus { border-color:var(--accent); background:var(--bg-2); }
.search-icon { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:var(--text-2); }
.suggest-drop { position:absolute; top:calc(100% + 4px); left:0; right:0; background:var(--bg-1); border:1.5px solid var(--accent); border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,.14); z-index:50; overflow:hidden; display:none; }
.suggest-drop.open { display:block; }
.suggest-item { display:flex; align-items:center; gap:10px; padding:10px 14px; cursor:pointer; border-bottom:1px solid var(--border); transition:.1s; }
.suggest-item:last-child { border-bottom:none; }
.suggest-item:hover { background:var(--bg-3); }
.si-avatar { width:32px; height:32px; border-radius:50%; background:var(--accent); color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; flex-shrink:0; }
.si-name { font-size:13px; font-weight:700; }
.si-meta { font-size:11px; color:var(--text-2); }

/* Student details card */
.stu-detail-card { border-radius:10px; background:var(--bg-3); padding:16px; margin-bottom:14px; display:none; }
.stu-detail-card.show { display:block; }
.stu-detail-top { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
.stu-detail-avatar { width:48px; height:48px; border-radius:50%; background:var(--accent); color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:800; flex-shrink:0; overflow:hidden; }
.stu-detail-avatar img { width:100%; height:100%; object-fit:cover; }
.stu-detail-name { font-size:15px; font-weight:800; }
.stu-detail-id { font-size:11px; color:var(--text-2); }
.stu-detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px 14px; }
.sdg-item .l { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-2); }
.sdg-item .v { font-size:12.5px; font-weight:600; margin-top:1px; }

/* Enrollment picker */
.enr-list { display:flex; flex-direction:column; gap:8px; margin-bottom:14px; }
.enr-item { border:1.5px solid var(--border); border-radius:10px; padding:12px 14px; cursor:pointer; transition:.12s; }
.enr-item:hover { border-color:var(--accent); }
.enr-item.selected { border-color:var(--accent); background:var(--bg-3); }
.enr-item.disabled { opacity:.55; cursor:not-allowed; }
.enr-item input[type=radio] { display:none; }
.enr-course { font-size:13px; font-weight:700; }
.enr-meta   { font-size:11px; color:var(--text-2); margin-top:3px; }
.enr-status { display:inline-block; font-size:10px; font-weight:800; padding:2px 8px; border-radius:5px; text-transform:uppercase; }
.es-run     { background:#fef9c3; color:#a16207; }
.es-open    { background:#dbeafe; color:#1e40af; }
.es-close   { background:#dcfce7; color:#15803d; }
.es-expired { background:#fee2e2; color:#991b1b; }
.enr-already { font-size:10.5px; font-weight:700; color:#dc2626; margin-top:4px; }

/* Form panel */
.form-panel { border-radius:14px; border:1.5px solid var(--border); background:var(--bg-2); overflow:hidden; }
.form-section { padding:20px 22px; border-bottom:1px solid var(--border); }
.form-section:last-child { border-bottom:none; }
.fs-title { font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:var(--text-2); margin-bottom:14px; }

/* Marks table */
.marks-table { width:100%; border-collapse:collapse; font-size:13px; }
.marks-table th { background:var(--bg-3); padding:8px 10px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-2); border-bottom:1px solid var(--border); }
.marks-table td { padding:8px 10px; border-bottom:1px solid var(--border); vertical-align:middle; }
.marks-table tr:last-child td { border-bottom:none; }
.marks-table input { padding:6px 8px; border:1.5px solid var(--border); border-radius:7px; font-size:13px; background:var(--bg-3); color:var(--text); outline:none; width:100%; box-sizing:border-box; }
.marks-table input:focus { border-color:var(--accent); }
.marks-table input[readonly] { background:var(--bg-3); color:var(--text-2); cursor:default; }
.no-subjects-note { font-size:12.5px; color:var(--text-2); padding:14px; text-align:center; background:var(--bg-3); border-radius:10px; }
.no-subjects-note a { color:var(--accent); font-weight:700; }

/* Result row */
.result-summary { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-top:14px; }
@media(max-width:560px){ .result-summary{grid-template-columns:1fr 1fr;} }
.rs-box { border:1.5px solid var(--border); border-radius:10px; padding:10px 12px; text-align:center; }
.rs-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-2); }
.rs-value { font-size:17px; font-weight:900; margin-top:3px; }

/* Submit bar */
.submit-bar { padding:16px 22px; display:flex; align-items:center; justify-content:space-between; background:var(--bg-3); }
.cert-preview-note { font-size:12px; color:var(--text-2); }
</style>
@endpush

@section('content')

<div class="gen-layout">

  {{-- ── LEFT: Search & Select ── --}}
  <div class="search-panel">
    <div class="panel-head">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      Select Student
    </div>
    <div class="panel-body">

      {{-- Search --}}
      <div class="search-wrap">
        <svg class="search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="stu-search" class="search-input" placeholder="Name / Mobile / Enrollment No…" autocomplete="off">
        <div class="suggest-drop" id="suggest-drop"></div>
      </div>

      {{-- Student details card --}}
      <div class="stu-detail-card" id="stu-card">
        <div class="stu-detail-top">
          <div class="stu-detail-avatar" id="stu-avatar">-</div>
          <div>
            <div class="stu-detail-name" id="stu-name">—</div>
            <div class="stu-detail-id" id="stu-uid">—</div>
          </div>
        </div>
        <div class="stu-detail-grid">
          <div class="sdg-item"><div class="l">Mobile</div><div class="v" id="d-mobile">—</div></div>
          <div class="sdg-item"><div class="l">Father's Name</div><div class="v" id="d-father">—</div></div>
          <div class="sdg-item"><div class="l">Mother's Name</div><div class="v" id="d-mother">—</div></div>
          <div class="sdg-item"><div class="l">Date of Birth</div><div class="v" id="d-dob">—</div></div>
        </div>
      </div>

      {{-- Enrollment list --}}
      <div id="enr-section" style="display:none;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-2);margin-bottom:8px;">Select Course</div>
        <div class="enr-list" id="enr-list"></div>
      </div>

      {{-- Walk-in link --}}
      <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border);text-align:center;">
        <div style="font-size:12px;color:var(--text-2);margin-bottom:8px;">Student enrolled nahi hai?</div>
        <a href="{{ route('institute.certificates.walkin') }}" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;">
          Walk-in Certificate →
        </a>
      </div>

    </div>
  </div>

  {{-- ── RIGHT: Form ── --}}
  <div id="form-area" style="display:none;">
  <form method="POST" action="{{ route('institute.certificates.store') }}" id="cert-form">
    @csrf
    <input type="hidden" name="course_book_id" id="f-course-book-id">
    <input type="hidden" name="is_walk_in" value="0">
    <input type="hidden" name="student_name" id="f-student-name">
    <input type="hidden" name="father_name" id="f-father-name">
    <input type="hidden" name="mother_name" id="f-mother-name">
    <input type="hidden" name="mobile" id="f-mobile">
    <input type="hidden" name="dob" id="f-dob">
    <input type="hidden" name="enrollment_no" id="f-enrollment-no">
    <input type="hidden" name="course_name" id="f-course-name">
    <input type="hidden" name="duration" id="f-duration">
    <input type="hidden" name="start_date" id="f-start-date">
    <input type="hidden" name="end_date" id="f-end-date">
    <input type="hidden" name="academic_session" id="f-academic-session">
    <input type="hidden" id="f-enr-status">

    <div class="form-panel">

      <div class="form-section">
        <div class="fs-title">Subject-wise Marks</div>
        <div id="subjects-wrap"></div>
        <div class="result-summary">
          <div class="rs-box"><div class="rs-label">Total Max</div><div class="rs-value" id="rs-max">0</div></div>
          <div class="rs-box"><div class="rs-label">Total Obtained</div><div class="rs-value" id="rs-obtained">0</div></div>
          <div class="rs-box"><div class="rs-label">Percentage</div><div class="rs-value" id="rs-percent">0%</div></div>
          <div class="rs-box"><div class="rs-label">Result</div><div class="rs-value" id="rs-result">—</div></div>
        </div>
      </div>

      <div class="submit-bar">
        <div class="cert-preview-note">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          Generate karne ke baad History list pe le jayega
        </div>
        <button type="submit" class="btn btn-primary" id="generate-btn" style="padding:10px 28px;">
          Next →
        </button>
      </div>

    </div>
  </form>
  </div>

  {{-- Placeholder when no student selected --}}
  <div id="form-placeholder" style="display:flex;align-items:center;justify-content:center;min-height:300px;">
    <div style="text-align:center;color:var(--text-2);">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="opacity:.3;margin-bottom:12px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      <div style="font-size:14px;font-weight:700;">Student search karo</div>
      <div style="font-size:12px;margin-top:4px;">Left side se student select karo,<br>phir course select kro.</div>
    </div>
  </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const SUGGEST_URL = '{{ route("institute.students.suggest") }}';

// ── Student Search (debounced) ──
const searchInput = document.getElementById('stu-search');
const suggestDrop = document.getElementById('suggest-drop');
let searchTimer;

searchInput.addEventListener('input', function() {
  clearTimeout(searchTimer);
  const q = this.value.trim();
  if (q.length < 2) { closeSuggest(); return; }
  searchTimer = setTimeout(() => fetchSuggest(q), 300);
});
document.addEventListener('click', e => { if (!e.target.closest('.search-wrap')) closeSuggest(); });

function fetchSuggest(q) {
  fetch(SUGGEST_URL + '?q=' + encodeURIComponent(q), { headers:{'X-Requested-With':'XMLHttpRequest'} })
    .then(r => r.json()).then(renderSuggest);
}

function renderSuggest(data) {
  if (!data.length) { suggestDrop.innerHTML='<div style="padding:14px;text-align:center;font-size:13px;color:var(--text-2)">No students found</div>'; suggestDrop.classList.add('open'); return; }
  suggestDrop.innerHTML = data.map(s => `
    <div class="suggest-item" onclick="selectStudent(${s.id})">
      <div class="si-avatar">${s.name.charAt(0).toUpperCase()}</div>
      <div><div class="si-name">${s.name}</div><div class="si-meta">${s.uid} · ${s.mobile}</div></div>
    </div>`).join('');
  suggestDrop.classList.add('open');
}

function closeSuggest() { suggestDrop.classList.remove('open'); }

// ── Select Student → load full details + enrollments ──
function selectStudent(id) {
  closeSuggest();

  fetch('{{ url("institute/certificates/enrollments") }}/' + id, { headers:{'X-Requested-With':'XMLHttpRequest'} })
    .then(r => r.json()).then(data => renderStudentAndEnrollments(data));
}

function renderStudentAndEnrollments(data) {
  const s = data.student;
  const enrollments = data.enrollments;

  document.getElementById('stu-card').classList.add('show');
  document.getElementById('stu-avatar').textContent = (s.name || '?').charAt(0).toUpperCase();
  document.getElementById('stu-name').textContent = s.name;
  document.getElementById('stu-uid').textContent = s.uid;
  document.getElementById('d-mobile').textContent = s.mobile || '—';
  document.getElementById('d-father').textContent = s.father_name || '—';
  document.getElementById('d-mother').textContent = s.mother_name || '—';
  document.getElementById('d-dob').textContent = s.dob || '—';

  document.getElementById('f-student-name').value = s.name || '';
  document.getElementById('f-father-name').value  = s.father_name || '';
  document.getElementById('f-mother-name').value  = s.mother_name || '';
  document.getElementById('f-mobile').value       = s.mobile || '';
  document.getElementById('f-dob').value          = s.dob || '';

  document.getElementById('enr-section').style.display = 'block';
  document.getElementById('form-area').style.display = 'none';
  document.getElementById('form-placeholder').style.display = 'flex';

  const list = document.getElementById('enr-list');
  if (!enrollments.length) {
    list.innerHTML = '<div style="font-size:13px;color:var(--text-2);text-align:center;padding:10px;">Koi enrollment nahi mila.<br>Walk-in option use karo.</div>';
    return;
  }

  const statusClass = { RUN:'es-run', OPEN:'es-open', CLOSE:'es-close', EXPIRED:'es-expired' };

  list.innerHTML = enrollments.map(e => `
    <label class="enr-item ${e.already_certified ? 'disabled' : ''}" ${e.already_certified ? '' : `onclick="selectEnrollment(this, ${JSON.stringify(e).replace(/"/g,'&quot;')})"`}>
      <input type="radio" name="enr_pick" value="${e.id}">
      <div class="enr-course">${e.course_name}</div>
      <div class="enr-meta">${e.enrollment_no || 'No Enrollment No'} · <span class="enr-status ${statusClass[e.status] || ''}">${e.status}</span></div>
      ${e.already_certified ? '<div class="enr-already">Certificate already generated for this course</div>' : ''}
    </label>`).join('');
}

let selectedSubjects = [];

function selectEnrollment(el, enr) {
  document.querySelectorAll('.enr-item').forEach(i => i.classList.remove('selected'));
  el.classList.add('selected');

  document.getElementById('f-course-book-id').value   = enr.id;
  document.getElementById('f-enr-status').value       = enr.status;
  document.getElementById('f-enrollment-no').value    = enr.enrollment_no || '';
  document.getElementById('f-course-name').value      = enr.course_name;
  document.getElementById('f-duration').value         = enr.duration || '';
  document.getElementById('f-start-date').value       = enr.start_date || '';
  document.getElementById('f-end-date').value         = enr.end_date || '';
  document.getElementById('f-academic-session').value = enr.academic_session || '';

  selectedSubjects = enr.subjects || [];
  renderSubjectsTable();

  document.getElementById('form-area').style.display = 'block';
  document.getElementById('form-placeholder').style.display = 'none';
}

function renderSubjectsTable() {
  const wrap = document.getElementById('subjects-wrap');
  if (!selectedSubjects.length) {
    wrap.innerHTML = '<div class="no-subjects-note">Is course ke liye subjects bind nahi hain — <a href="{{ route("institute.subjects.bind") }}" target="_blank">Subjects → Bind to Courses</a> se add karo.</div>';
    calcTotals();
    return;
  }
  wrap.innerHTML = `
    <div style="overflow-x:auto;">
    <table class="marks-table" id="marks-table">
      <thead>
        <tr>
          <th style="width:14%">Code</th>
          <th style="width:38%">Subject</th>
          <th style="width:16%">Max</th>
          <th style="width:16%">Obtained</th>
          <th style="width:16%">Grade</th>
        </tr>
      </thead>
      <tbody id="marks-body">
        ${selectedSubjects.map((s, i) => `
          <tr>
            <td><input type="text" value="${s.code || ''}" readonly></td>
            <td><input type="text" value="${s.name || ''}" readonly>
              <input type="hidden" name="subjects[${i}][name]" value="${s.name || ''}">
              <input type="hidden" name="subjects[${i}][code]" value="${s.code || ''}">
              <input type="hidden" name="subjects[${i}][subject_id]" value="${s.subject_id || ''}">
              <input type="hidden" name="subjects[${i}][max]" value="${s.max || 0}">
            </td>
            <td><input type="text" value="${s.max || 0}" readonly></td>
            <td><input type="number" name="subjects[${i}][obtained]" min="0" max="${s.max || ''}" placeholder="0" oninput="calcTotals()"></td>
            <td class="subj-grade" data-max="${s.max || 0}">—</td>
          </tr>`).join('')}
      </tbody>
    </table>
    </div>`;
  calcTotals();
}

function gradeFor(percent) {
  if (percent >= 90) return 'A+';
  if (percent >= 75) return 'A';
  if (percent >= 60) return 'B';
  if (percent >= 45) return 'C';
  if (percent >= 35) return 'D';
  return 'F';
}

function calcTotals() {
  let maxT = 0, obtT = 0;
  document.querySelectorAll('#marks-body tr').forEach(row => {
    const mx = parseFloat(row.querySelector('[name*="[max]"]')?.value) || 0;
    const obInput = row.querySelector('[name*="[obtained]"]');
    const ob = parseFloat(obInput?.value) || 0;
    maxT += mx; obtT += ob;
    const gradeCell = row.querySelector('.subj-grade');
    if (gradeCell) gradeCell.textContent = mx > 0 && obInput?.value !== '' ? gradeFor((ob / mx) * 100) : '—';
  });
  document.getElementById('rs-max').textContent = maxT;
  document.getElementById('rs-obtained').textContent = obtT;
  const pct = maxT > 0 ? (obtT / maxT * 100) : 0;
  document.getElementById('rs-percent').textContent = pct.toFixed(2) + '%';
  document.getElementById('rs-result').textContent = maxT > 0 ? (pct < 35 ? 'FAIL' : 'PASS') : '—';
}

// ── Submit: confirm if course status isn't CLOSE ──
document.getElementById('cert-form').addEventListener('submit', function(e) {
  const status = document.getElementById('f-enr-status').value;
  if (status && status !== 'CLOSE') {
    e.preventDefault();
    Swal.fire({
      icon: 'warning',
      title: 'Course Complete Nahi Hua Hai',
      text: `Is course ka status abhi "${status}" hai, complete nahi hua hai. Kya phir bhi certificate generate karna chahte ho?`,
      showCancelButton: true,
      confirmButtonText: 'Haan, Generate Karo',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#6c5dd3',
    }).then(result => {
      if (result.isConfirmed) {
        document.getElementById('cert-form').submit();
      }
    });
  }
});
</script>
@endpush
