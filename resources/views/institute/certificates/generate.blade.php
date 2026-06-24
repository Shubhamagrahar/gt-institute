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

/* Student card */
.stu-card { border-radius:10px; background:var(--bg-3); padding:14px; margin-bottom:14px; display:none; }
.stu-card.show { display:block; }
.stu-card-name { font-size:15px; font-weight:800; }
.stu-card-meta { font-size:12px; color:var(--text-2); margin-top:3px; }

/* Enrollment picker */
.enr-list { display:flex; flex-direction:column; gap:8px; margin-bottom:14px; }
.enr-item { border:1.5px solid var(--border); border-radius:10px; padding:12px 14px; cursor:pointer; transition:.12s; }
.enr-item:hover { border-color:var(--accent); }
.enr-item.selected { border-color:var(--accent); background:var(--bg-3); }
.enr-item input[type=radio] { display:none; }
.enr-course { font-size:13px; font-weight:700; }
.enr-meta   { font-size:11px; color:var(--text-2); margin-top:3px; }
.enr-status { display:inline-block; font-size:10px; font-weight:800; padding:2px 8px; border-radius:5px; text-transform:uppercase; }
.es-close { background:#dcfce7; color:#15803d; }
.es-run   { background:#fef9c3; color:#a16207; }

/* Doc type buttons */
.doc-type-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px; margin-bottom:14px; }
.doc-type-btn { border:1.5px solid var(--border); border-radius:10px; padding:10px 6px; text-align:center; cursor:pointer; transition:.12s; }
.doc-type-btn:hover { border-color:var(--accent); }
.doc-type-btn.selected { border-color:var(--accent); background:var(--bg-3); }
.doc-type-btn input[type=radio] { display:none; }
.dtb-label { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; }
.dtb-name  { font-size:12px; font-weight:700; margin-top:3px; }
.dtb-ms { color:#5b21b6; }
.dtb-tc { color:#1e40af; }
.dtb-cc { color:#15803d; }

/* Form panel */
.form-panel { border-radius:14px; border:1.5px solid var(--border); background:var(--bg-2); overflow:hidden; }
.form-section { padding:20px 22px; border-bottom:1px solid var(--border); }
.form-section:last-child { border-bottom:none; }
.fs-title { font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:var(--text-2); margin-bottom:14px; }
.form-row { display:grid; gap:12px; margin-bottom:12px; }
.form-row.cols2 { grid-template-columns:1fr 1fr; }
.form-row.cols3 { grid-template-columns:1fr 1fr 1fr; }
.fg label { font-size:11px; font-weight:700; color:var(--text-2); text-transform:uppercase; letter-spacing:.4px; display:block; margin-bottom:5px; }
.fg input, .fg select, .fg textarea {
  width:100%; padding:9px 12px; border:1.5px solid var(--border); border-radius:9px;
  font-size:13px; background:var(--bg-3); color:var(--text); outline:none;
  transition:.15s; box-sizing:border-box; }
.fg input:focus, .fg select:focus, .fg textarea:focus { border-color:var(--accent); background:var(--bg-2); }

/* Marks table */
.marks-table { width:100%; border-collapse:collapse; font-size:13px; }
.marks-table th { background:var(--bg-3); padding:8px 10px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-2); border-bottom:1px solid var(--border); }
.marks-table td { padding:8px 10px; border-bottom:1px solid var(--border); }
.marks-table tr:last-child td { border-bottom:none; }
.marks-table input { padding:6px 8px; border:1.5px solid var(--border); border-radius:7px; font-size:13px; background:var(--bg-3); color:var(--text); outline:none; width:100%; box-sizing:border-box; }
.marks-table input:focus { border-color:var(--accent); }
.add-subject-btn { margin-top:10px; padding:7px 14px; border:1.5px dashed var(--border); border-radius:8px; background:transparent; color:var(--text-2); font-size:12px; font-weight:600; cursor:pointer; width:100%; transition:.12s; }
.add-subject-btn:hover { border-color:var(--accent); color:var(--accent); }
.remove-row { background:none; border:none; cursor:pointer; color:#ef4444; padding:0 6px; font-size:16px; line-height:1; }

/* Result row */
.result-chips { display:flex; gap:8px; }
.result-chip { flex:1; border:1.5px solid var(--border); border-radius:8px; padding:8px; text-align:center; cursor:pointer; transition:.12s; font-size:12px; font-weight:700; }
.result-chip input[type=radio] { display:none; }
.result-chip.sel-pass { border-color:#16a34a; background:#dcfce7; color:#15803d; }
.result-chip.sel-fail { border-color:#dc2626; background:#fee2e2; color:#991b1b; }
.result-chip:not(.sel-pass):not(.sel-fail):hover { border-color:var(--accent); }

/* Submit bar */
.submit-bar { padding:16px 22px; display:flex; align-items:center; justify-content:space-between; background:var(--bg-3); }
.cert-preview-note { font-size:12px; color:var(--text-2); }

/* Hidden sections */
.section-marksheet, .section-tc, .section-cc { display:none; }
.section-marksheet.show, .section-tc.show, .section-cc.show { display:block; }
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

      {{-- Selected student card --}}
      <div class="stu-card" id="stu-card">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="width:38px;height:38px;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;flex-shrink:0;" id="stu-avatar">-</div>
          <div>
            <div class="stu-card-name" id="stu-name">—</div>
            <div class="stu-card-meta" id="stu-meta">—</div>
          </div>
        </div>
      </div>

      {{-- Enrollment list --}}
      <div id="enr-section" style="display:none;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-2);margin-bottom:8px;">Select Enrollment</div>
        <div class="enr-list" id="enr-list"></div>
      </div>

      {{-- Doc type --}}
      <div id="doctype-section" style="display:none;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-2);margin-bottom:8px;">Document Type</div>
        <div class="doc-type-grid">
          <label class="doc-type-btn" id="btn-ms">
            <input type="radio" name="doc_type" value="MARKSHEET" onchange="switchDocType('MARKSHEET')">
            <div class="dtb-label dtb-ms">MS</div>
            <div class="dtb-name">Marksheet</div>
          </label>
          <label class="doc-type-btn" id="btn-tc">
            <input type="radio" name="doc_type" value="TC" onchange="switchDocType('TC')">
            <div class="dtb-label dtb-tc">TC</div>
            <div class="dtb-name">Transfer<br>Cert.</div>
          </label>
          <label class="doc-type-btn" id="btn-cc">
            <input type="radio" name="doc_type" value="CC" onchange="switchDocType('CC')">
            <div class="dtb-label dtb-cc">CC</div>
            <div class="dtb-name">Character<br>Cert.</div>
          </label>
        </div>
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
    <input type="hidden" name="doc_type" id="f-doc-type">
    <input type="hidden" name="is_walk_in" value="0">

    <div class="form-panel">

      {{-- Student info (pre-filled, read-only) --}}
      <div class="form-section">
        <div class="fs-title">Student Information</div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Student Name</label>
            <input type="text" name="student_name" id="f-student-name" required>
          </div>
          <div class="fg">
            <label>Father's Name</label>
            <input type="text" name="father_name" id="f-father-name">
          </div>
        </div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Enrollment / Cert No.</label>
            <input type="text" name="enrollment_no" id="f-enrollment-no" readonly style="background:var(--bg-3);color:var(--text-2);">
          </div>
          <div class="fg">
            <label>Mobile</label>
            <input type="text" name="mobile" id="f-mobile">
          </div>
        </div>
      </div>

      {{-- Course info --}}
      <div class="form-section">
        <div class="fs-title">Course Details</div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Course Name</label>
            <input type="text" name="course_name" id="f-course-name" required>
          </div>
          <div class="fg">
            <label>Duration</label>
            <input type="text" name="duration" id="f-duration" placeholder="e.g. 6 Months">
          </div>
        </div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Start Date</label>
            <input type="date" name="start_date" id="f-start-date">
          </div>
          <div class="fg">
            <label>End Date</label>
            <input type="date" name="end_date" id="f-end-date">
          </div>
        </div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Academic Session</label>
            <input type="text" name="academic_session" placeholder="e.g. 2025-26">
          </div>
          <div class="fg">
            <label>Passing Year</label>
            <input type="text" name="passing_year" id="f-passing-year" placeholder="e.g. 2026">
          </div>
        </div>
      </div>

      {{-- ── MARKSHEET SECTION ── --}}
      <div class="form-section section-marksheet" id="section-marksheet">
        <div class="fs-title">Subject-wise Marks</div>
        <div style="overflow-x:auto;">
          <table class="marks-table" id="marks-table">
            <thead>
              <tr>
                <th style="width:34%">Subject</th>
                <th style="width:18%">Max Marks</th>
                <th style="width:18%">Obtained</th>
                <th style="width:18%">Grade</th>
                <th style="width:12%"></th>
              </tr>
            </thead>
            <tbody id="marks-body">
              <tr>
                <td><input type="text" name="subjects[0][name]" placeholder="Subject name"></td>
                <td><input type="number" name="subjects[0][max]" placeholder="100" min="0"></td>
                <td><input type="number" name="subjects[0][obtained]" placeholder="75" min="0" oninput="calcTotals()"></td>
                <td><input type="text" name="subjects[0][grade]" placeholder="A"></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
        <button type="button" class="add-subject-btn" onclick="addSubjectRow()">+ Add Subject</button>

        <div class="form-row cols3" style="margin-top:16px;">
          <div class="fg">
            <label>Total Max Marks</label>
            <input type="number" name="total_max" id="total-max" readonly style="background:var(--bg-3);">
          </div>
          <div class="fg">
            <label>Total Obtained</label>
            <input type="number" name="total_obtained" id="total-obtained" readonly style="background:var(--bg-3);">
          </div>
          <div class="fg">
            <label>Percentage</label>
            <input type="text" name="percentage" id="percentage" readonly style="background:var(--bg-3);">
          </div>
        </div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Overall Grade</label>
            <select name="overall_grade">
              <option value="">— Select —</option>
              <option>A+</option><option>A</option><option>B+</option>
              <option>B</option><option>C</option><option>D</option>
            </select>
          </div>
          <div class="fg">
            <label>Result</label>
            <div class="result-chips" style="margin-top:5px;">
              <label class="result-chip" id="chip-pass" onclick="this.classList.add('sel-pass');document.getElementById('chip-fail').classList.remove('sel-fail')">
                <input type="radio" name="result" value="PASS" checked> PASS
              </label>
              <label class="result-chip" id="chip-fail" onclick="this.classList.add('sel-fail');document.getElementById('chip-pass').classList.remove('sel-pass')">
                <input type="radio" name="result" value="FAIL"> FAIL
              </label>
            </div>
          </div>
        </div>
      </div>

      {{-- ── TC SECTION ── --}}
      <div class="form-section section-tc" id="section-tc">
        <div class="fs-title">Transfer Certificate Details</div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Reason for Leaving</label>
            <select name="tc_reason">
              <option value="Course Completed">Course Completed</option>
              <option value="Transfer">Transfer</option>
              <option value="Personal Reasons">Personal Reasons</option>
            </select>
          </div>
          <div class="fg">
            <label>Conduct</label>
            <select name="tc_conduct">
              <option>Good</option><option>Satisfactory</option><option>Excellent</option>
            </select>
          </div>
        </div>
      </div>

      {{-- ── CC SECTION ── --}}
      <div class="form-section section-cc" id="section-cc">
        <div class="fs-title">Character Certificate Details</div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Character Grade</label>
            <select name="character_grade">
              <option>Satisfactory</option><option>Good</option><option>Excellent</option><option>Very Good</option>
            </select>
          </div>
          <div class="fg">
            <label>Conduct</label>
            <select name="cc_conduct">
              <option>Good</option><option>Excellent</option><option>Satisfactory</option>
            </select>
          </div>
        </div>
      </div>

      {{-- Submit bar --}}
      <div class="submit-bar">
        <div class="cert-preview-note">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          PDF new tab mein khulega — auto print
        </div>
        <button type="submit" class="btn btn-primary" id="generate-btn" style="padding:10px 28px;">
          Generate &amp; Print
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
      <div style="font-size:12px;margin-top:4px;">Left side se student select karo,<br>phir document type aur details fill karo.</div>
    </div>
  </div>

</div>

@endsection

@push('scripts')
<script>
const SUGGEST_URL = '{{ route("institute.students.suggest") }}';
let subjectCount = 1;

// ── Student Search ──
const searchInput = document.getElementById('stu-search');
const suggestDrop = document.getElementById('suggest-drop');
let searchTimer;

searchInput.addEventListener('input', function() {
  clearTimeout(searchTimer);
  const q = this.value.trim();
  if (q.length < 2) { closeSuggest(); return; }
  searchTimer = setTimeout(() => fetchSuggest(q), 250);
});
document.addEventListener('click', e => { if (!e.target.closest('.search-wrap')) closeSuggest(); });

function fetchSuggest(q) {
  fetch(SUGGEST_URL + '?q=' + encodeURIComponent(q), { headers:{'X-Requested-With':'XMLHttpRequest'} })
    .then(r => r.json()).then(renderSuggest);
}

function renderSuggest(data) {
  if (!data.length) { suggestDrop.innerHTML='<div style="padding:14px;text-align:center;font-size:13px;color:var(--text-2)">No students found</div>'; suggestDrop.classList.add('open'); return; }
  suggestDrop.innerHTML = data.map(s => `
    <div class="suggest-item" onclick="selectStudent(${s.id},'${s.name}','${s.mobile}','${s.uid}')">
      <div class="si-avatar">${s.name.charAt(0).toUpperCase()}</div>
      <div><div class="si-name">${s.name}</div><div class="si-meta">${s.uid} · ${s.mobile}</div></div>
    </div>`).join('');
  suggestDrop.classList.add('open');
}

function closeSuggest() { suggestDrop.classList.remove('open'); }

// ── Select Student → load enrollments ──
function selectStudent(id, name, mobile, uid) {
  closeSuggest();
  searchInput.value = name;

  document.getElementById('stu-card').classList.add('show');
  document.getElementById('stu-avatar').textContent = name.charAt(0).toUpperCase();
  document.getElementById('stu-name').textContent = name;
  document.getElementById('stu-meta').textContent = uid + ' · ' + mobile;

  document.getElementById('f-student-name').value = name;
  document.getElementById('f-mobile').value = mobile;

  fetch('{{ url("institute/certificates/enrollments") }}/' + id, { headers:{'X-Requested-With':'XMLHttpRequest'} })
    .then(r => r.json()).then(renderEnrollments);
}

function renderEnrollments(enrollments) {
  const list = document.getElementById('enr-list');
  const sec  = document.getElementById('enr-section');
  document.getElementById('doctype-section').style.display = 'none';
  document.getElementById('form-area').style.display = 'none';
  document.getElementById('form-placeholder').style.display = 'flex';

  if (!enrollments.length) {
    list.innerHTML = '<div style="font-size:13px;color:var(--text-2);text-align:center;padding:10px;">No CLOSE enrollments found.<br>Walk-in option use karo.</div>';
    sec.style.display = 'block';
    return;
  }
  list.innerHTML = enrollments.map(e => `
    <label class="enr-item" onclick="selectEnrollment(this, ${JSON.stringify(e).replace(/"/g,'&quot;')})">
      <input type="radio" name="enr_pick" value="${e.id}">
      <div class="enr-course">${e.course_name}</div>
      <div class="enr-meta">${e.enrollment_no || 'No Enrollment No'} · <span class="enr-status es-close">Completed</span></div>
    </label>`).join('');
  sec.style.display = 'block';
}

function selectEnrollment(el, enr) {
  document.querySelectorAll('.enr-item').forEach(i => i.classList.remove('selected'));
  el.classList.add('selected');

  document.getElementById('f-course-book-id').value = enr.id;
  document.getElementById('f-enrollment-no').value  = enr.enrollment_no || '';
  document.getElementById('f-course-name').value    = enr.course_name;
  document.getElementById('f-start-date').value     = enr.start_date || '';
  document.getElementById('f-end-date').value       = enr.end_date || '';
  document.getElementById('f-father-name').value    = enr.father_name || '';
  if (enr.end_date) document.getElementById('f-passing-year').value = enr.end_date.substring(0,4);

  document.getElementById('doctype-section').style.display = 'block';
}

// ── Doc type switch ──
function switchDocType(type) {
  document.getElementById('f-doc-type').value = type;
  ['btn-ms','btn-tc','btn-cc'].forEach(id => document.getElementById(id).classList.remove('selected'));
  document.getElementById('btn-' + type.toLowerCase()).classList.add('selected');

  ['section-marksheet','section-tc','section-cc'].forEach(id => {
    document.getElementById(id).classList.remove('show');
  });
  if (type === 'MARKSHEET') document.getElementById('section-marksheet').classList.add('show');
  if (type === 'TC')        document.getElementById('section-tc').classList.add('show');
  if (type === 'CC')        document.getElementById('section-cc').classList.add('show');

  document.getElementById('form-area').style.display = 'block';
  document.getElementById('form-placeholder').style.display = 'none';
}

// ── Marks auto-calc ──
function calcTotals() {
  let maxT = 0, obtT = 0;
  document.querySelectorAll('#marks-body tr').forEach(row => {
    const mx = parseFloat(row.querySelector('[name*="[max]"]')?.value) || 0;
    const ob = parseFloat(row.querySelector('[name*="[obtained]"]')?.value) || 0;
    maxT += mx; obtT += ob;
  });
  document.getElementById('total-max').value = maxT || '';
  document.getElementById('total-obtained').value = obtT || '';
  document.getElementById('percentage').value = maxT > 0 ? (obtT/maxT*100).toFixed(2) + '%' : '';
}

function addSubjectRow() {
  const i = subjectCount++;
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input type="text" name="subjects[${i}][name]" placeholder="Subject name"></td>
    <td><input type="number" name="subjects[${i}][max]" placeholder="100" min="0"></td>
    <td><input type="number" name="subjects[${i}][obtained]" placeholder="0" min="0" oninput="calcTotals()"></td>
    <td><input type="text" name="subjects[${i}][grade]" placeholder="A"></td>
    <td><button type="button" class="remove-row" onclick="this.closest('tr').remove();calcTotals()">×</button></td>`;
  document.getElementById('marks-body').appendChild(tr);
}
</script>
@endpush
