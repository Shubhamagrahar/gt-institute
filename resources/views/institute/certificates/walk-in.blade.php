@extends('layouts.institute')
@section('title','Walk-in Certificate')
@section('page-title','Walk-in Certificate')

@push('styles')
<style>
.walkin-wrap { max-width:780px; margin:0 auto; }
.info-banner { background:linear-gradient(135deg,#fffbeb,#fef3c7); border:1.5px solid #fbbf24; border-radius:12px; padding:14px 18px; margin-bottom:22px; display:flex; align-items:flex-start; gap:10px; }
.info-banner svg { flex-shrink:0; margin-top:1px; stroke:#d97706; }
.info-banner-txt { font-size:13px; font-weight:600; color:#78350f; }
.info-banner-sub { font-size:12px; color:#92400e; margin-top:3px; }
.form-panel { border-radius:14px; border:1.5px solid var(--border); background:var(--bg-2); overflow:hidden; }
.form-section { padding:20px 22px; border-bottom:1px solid var(--border); }
.form-section:last-child { border-bottom:none; }
.fs-title { font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:var(--text-2); margin-bottom:14px; }
.form-row { display:grid; gap:12px; margin-bottom:12px; }
.form-row.cols2 { grid-template-columns:1fr 1fr; }
.form-row.cols3 { grid-template-columns:1fr 1fr 1fr; }
@media(max-width:600px){ .form-row.cols2,.form-row.cols3{grid-template-columns:1fr;} }
.fg label { font-size:11px; font-weight:700; color:var(--text-2); text-transform:uppercase; letter-spacing:.4px; display:block; margin-bottom:5px; }
.fg input,.fg select,.fg textarea { width:100%; padding:9px 12px; border:1.5px solid var(--border); border-radius:9px; font-size:13px; background:var(--bg-3); color:var(--text); outline:none; transition:.15s; box-sizing:border-box; }
.fg input:focus,.fg select:focus { border-color:var(--accent); background:var(--bg-2); }
/* Doc type selector */
.doc-type-row { display:flex; gap:10px; margin-bottom:4px; }
.doc-radio { flex:1; border:1.5px solid var(--border); border-radius:10px; padding:12px; text-align:center; cursor:pointer; transition:.12s; }
.doc-radio input { display:none; }
.doc-radio:has(input:checked) { border-color:var(--accent); background:var(--bg-3); }
.doc-radio .dr-code { font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.8px; }
.doc-radio .dr-name { font-size:12px; font-weight:700; margin-top:3px; }
.dr-ms { color:#5b21b6; } .dr-tc { color:#1e40af; } .dr-cc { color:#15803d; }
/* Marks table */
.marks-table { width:100%; border-collapse:collapse; font-size:13px; }
.marks-table th { background:var(--bg-3); padding:8px 10px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--text-2); border-bottom:1px solid var(--border); }
.marks-table td { padding:8px 10px; border-bottom:1px solid var(--border); }
.marks-table tr:last-child td { border-bottom:none; }
.marks-table input { padding:6px 8px; border:1.5px solid var(--border); border-radius:7px; font-size:13px; background:var(--bg-3); color:var(--text); outline:none; width:100%; box-sizing:border-box; }
.marks-table input:focus { border-color:var(--accent); }
.add-subject-btn { margin-top:10px; padding:7px 14px; border:1.5px dashed var(--border); border-radius:8px; background:transparent; color:var(--text-2); font-size:12px; font-weight:600; cursor:pointer; width:100%; transition:.12s; }
.add-subject-btn:hover { border-color:var(--accent); color:var(--accent); }
.remove-row { background:none; border:none; cursor:pointer; color:#ef4444; padding:0 6px; font-size:16px; }
/* Section toggle */
.ms-only, .tc-only, .cc-only { display:none; }
.submit-bar { padding:16px 22px; display:flex; align-items:center; justify-content:space-between; background:var(--bg-3); }
</style>
@endpush

@section('content')

<div class="walkin-wrap">

  <div class="info-banner">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
    <div>
      <div class="info-banner-txt">Walk-in Certificate</div>
      <div class="info-banner-sub">Ye option un students ke liye hai jinhone system mein enrollment nahi kiya hai — alag center/course se aaye hain ya directly certificate chahiye. Saare details manually bharo.</div>
    </div>
  </div>

  <form method="POST" action="{{ route('institute.certificates.store') }}">
    @csrf
    <input type="hidden" name="is_walk_in" value="1">
    <input type="hidden" name="doc_type" id="f-doc-type" value="MARKSHEET">

    <div class="form-panel">

      {{-- Document Type --}}
      <div class="form-section">
        <div class="fs-title">Document Type</div>
        <div class="doc-type-row">
          <label class="doc-radio">
            <input type="radio" name="doc_type_sel" value="MARKSHEET" checked onchange="switchType('MARKSHEET')">
            <div class="dr-code dr-ms">MS</div>
            <div class="dr-name">Marksheet</div>
          </label>
          <label class="doc-radio">
            <input type="radio" name="doc_type_sel" value="TC" onchange="switchType('TC')">
            <div class="dr-code dr-tc">TC</div>
            <div class="dr-name">Transfer Cert.</div>
          </label>
          <label class="doc-radio">
            <input type="radio" name="doc_type_sel" value="CC" onchange="switchType('CC')">
            <div class="dr-code dr-cc">CC</div>
            <div class="dr-name">Character Cert.</div>
          </label>
        </div>
      </div>

      {{-- Student Details --}}
      <div class="form-section">
        <div class="fs-title">Student Details</div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Student Full Name <span style="color:#ef4444">*</span></label>
            <input type="text" name="student_name" required placeholder="e.g. Rahul Sharma">
          </div>
          <div class="fg">
            <label>Father's Name</label>
            <input type="text" name="father_name" placeholder="e.g. Ramesh Sharma">
          </div>
        </div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Mobile</label>
            <input type="text" name="mobile" placeholder="10-digit mobile">
          </div>
          <div class="fg">
            <label>Date of Birth</label>
            <input type="date" name="dob">
          </div>
        </div>
      </div>

      {{-- Course Details --}}
      <div class="form-section">
        <div class="fs-title">Course Details</div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Course Name <span style="color:#ef4444">*</span></label>
            <input type="text" name="course_name" required placeholder="e.g. Computer Fundamentals">
          </div>
          <div class="fg">
            <label>Duration</label>
            <input type="text" name="duration" placeholder="e.g. 6 Months">
          </div>
        </div>
        <div class="form-row cols3">
          <div class="fg">
            <label>Start Date</label>
            <input type="date" name="start_date">
          </div>
          <div class="fg">
            <label>End Date</label>
            <input type="date" name="end_date" id="wi-end-date" oninput="autoYear()">
          </div>
          <div class="fg">
            <label>Academic Session</label>
            <input type="text" name="academic_session" placeholder="e.g. 2025-26">
          </div>
        </div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Passing Year</label>
            <input type="text" name="passing_year" id="wi-passing-year" placeholder="e.g. 2026">
          </div>
          <div class="fg">
            <label>Reference / Reg. No. <small style="font-weight:400">(optional)</small></label>
            <input type="text" name="ref_no" placeholder="External reg. number if any">
          </div>
        </div>
      </div>

      {{-- MARKSHEET section --}}
      <div class="form-section ms-only" id="ms-section" style="display:block;">
        <div class="fs-title">Subject-wise Marks</div>
        <div style="overflow-x:auto;">
          <table class="marks-table">
            <thead>
              <tr>
                <th style="width:35%">Subject</th>
                <th style="width:18%">Max</th>
                <th style="width:18%">Obtained</th>
                <th style="width:17%">Grade</th>
                <th style="width:12%"></th>
              </tr>
            </thead>
            <tbody id="wi-marks-body">
              <tr>
                <td><input type="text" name="subjects[0][name]" placeholder="Subject name"></td>
                <td><input type="number" name="subjects[0][max]" placeholder="100" oninput="wiCalc()"></td>
                <td><input type="number" name="subjects[0][obtained]" placeholder="75" oninput="wiCalc()"></td>
                <td><input type="text" name="subjects[0][grade]" placeholder="A"></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
        <button type="button" class="add-subject-btn" onclick="wiAddRow()">+ Add Subject</button>
        <div class="form-row cols3" style="margin-top:14px;">
          <div class="fg"><label>Total Max</label><input type="number" id="wi-max" name="total_max" readonly style="background:var(--bg-3);"></div>
          <div class="fg"><label>Total Obtained</label><input type="number" id="wi-obt" name="total_obtained" readonly style="background:var(--bg-3);"></div>
          <div class="fg"><label>Percentage</label><input type="text" id="wi-pct" name="percentage" readonly style="background:var(--bg-3);"></div>
        </div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Overall Grade</label>
            <select name="overall_grade">
              <option>A+</option><option>A</option><option>B+</option><option>B</option><option>C</option><option>D</option>
            </select>
          </div>
          <div class="fg">
            <label>Result</label>
            <select name="result">
              <option value="PASS">PASS</option>
              <option value="FAIL">FAIL</option>
            </select>
          </div>
        </div>
      </div>

      {{-- TC section --}}
      <div class="form-section tc-only" id="tc-section">
        <div class="fs-title">TC Details</div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Reason for Leaving</label>
            <select name="tc_reason">
              <option>Course Completed</option><option>Transfer</option><option>Personal Reasons</option>
            </select>
          </div>
          <div class="fg">
            <label>Conduct</label>
            <select name="tc_conduct"><option>Good</option><option>Satisfactory</option><option>Excellent</option></select>
          </div>
        </div>
      </div>

      {{-- CC section --}}
      <div class="form-section cc-only" id="cc-section">
        <div class="fs-title">Character Certificate Details</div>
        <div class="form-row cols2">
          <div class="fg">
            <label>Character Grade</label>
            <select name="character_grade"><option>Satisfactory</option><option>Good</option><option>Excellent</option></select>
          </div>
          <div class="fg">
            <label>Conduct</label>
            <select name="cc_conduct"><option>Good</option><option>Excellent</option><option>Satisfactory</option></select>
          </div>
        </div>
      </div>

      {{-- Submit --}}
      <div class="submit-bar">
        <a href="{{ route('institute.certificates.generate') }}" class="btn btn-outline btn-sm">← Enrolled Student</a>
        <button type="submit" class="btn btn-primary" style="padding:10px 28px;">
          Generate &amp; Print
        </button>
      </div>

    </div>
  </form>
</div>

@endsection

@push('scripts')
<script>
let wiRowCount = 1;

function switchType(t) {
  document.getElementById('f-doc-type').value = t;
  document.getElementById('ms-section').style.display = t === 'MARKSHEET' ? 'block' : 'none';
  document.getElementById('tc-section').style.display = t === 'TC'        ? 'block' : 'none';
  document.getElementById('cc-section').style.display = t === 'CC'        ? 'block' : 'none';
}

function autoYear() {
  const v = document.getElementById('wi-end-date').value;
  if (v) document.getElementById('wi-passing-year').value = v.substring(0,4);
}

function wiCalc() {
  let mx = 0, ob = 0;
  document.querySelectorAll('#wi-marks-body tr').forEach(row => {
    mx += parseFloat(row.querySelector('[name*="[max]"]')?.value) || 0;
    ob += parseFloat(row.querySelector('[name*="[obtained]"]')?.value) || 0;
  });
  document.getElementById('wi-max').value = mx || '';
  document.getElementById('wi-obt').value = ob || '';
  document.getElementById('wi-pct').value = mx > 0 ? (ob/mx*100).toFixed(2) + '%' : '';
}

function wiAddRow() {
  const i = wiRowCount++;
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input type="text" name="subjects[${i}][name]" placeholder="Subject name"></td>
    <td><input type="number" name="subjects[${i}][max]" placeholder="100" oninput="wiCalc()"></td>
    <td><input type="number" name="subjects[${i}][obtained]" placeholder="0" oninput="wiCalc()"></td>
    <td><input type="text" name="subjects[${i}][grade]" placeholder="A"></td>
    <td><button type="button" class="remove-row" onclick="this.closest('tr').remove();wiCalc()">×</button></td>`;
  document.getElementById('wi-marks-body').appendChild(tr);
}
</script>
@endpush
