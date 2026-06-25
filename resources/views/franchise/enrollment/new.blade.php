@extends('layouts.franchise')
@section('title','New Admission')
@section('page-title','New Admission')
@section('topbar-actions')
  <a href="{{ route('franchise.enrollment.choose') }}" class="btn btn-outline btn-sm">← Back</a>
@endsection

@push('styles')
<style>
.adm-shell{max-width:1100px;margin:0 auto}
.adm-header{background:linear-gradient(135deg,#ea580c,#c2410c);color:#fff;border-radius:22px;padding:26px 30px;box-shadow:0 20px 45px rgba(234,88,12,.18)}
.adm-header h2{margin:0;font-size:28px;font-weight:900}
.adm-header p{margin:8px 0 0;opacity:.84;font-size:13px}
.adm-wrap{display:block;margin-top:18px}
.adm-card{background:var(--bg-2);border:1px solid var(--border);border-radius:20px;overflow:hidden}
.adm-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;padding:22px;background:rgba(234,88,12,.04);border-bottom:1px solid var(--border)}
.adm-step{padding:12px 8px;border-radius:14px;background:rgba(234,88,12,.08);color:rgba(234,88,12,.5);text-align:center;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;transition:.2s}
.adm-step.active{background:#ea580c;color:#fff}
.adm-step.done{background:rgba(234,88,12,.15);color:#ea580c}
.adm-body{padding:24px}
.wizard-step{display:none}
.wizard-step.active{display:block}
.adm-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.adm-grid-3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.adm-section-title{font-size:20px;font-weight:900;margin:0 0 6px;color:var(--text-1)}
.adm-section-note{font-size:13px;color:var(--text-2);margin-bottom:20px}
.fee-preview-box{background:rgba(234,88,12,.04);border:1px solid rgba(234,88,12,.15);border-radius:18px;padding:18px;margin-top:20px}
.fee-preview-title{font-size:11px;font-weight:800;color:var(--text-3);text-transform:uppercase;letter-spacing:.1em;margin-bottom:12px}
.fee-preview-line{display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid var(--border-2)}
.fee-preview-line:last-child{border-bottom:none}
.fee-preview-total{margin-top:12px;padding-top:12px;border-top:1px solid rgba(234,88,12,.2);font-size:17px;font-weight:900;color:#ea580c}
.wizard-actions{display:flex;justify-content:space-between;gap:12px;margin-top:28px}
.review-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.review-section{background:var(--bg-3);border:1px solid var(--border);border-radius:14px;padding:16px}
.review-section-title{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--text-3);margin-bottom:12px}
.review-row{display:flex;justify-content:space-between;gap:12px;padding:6px 0;border-bottom:1px solid var(--border-2);font-size:13px}
.review-row:last-child{border-bottom:none}
.review-row strong{font-weight:700;color:#ea580c;text-align:right;max-width:60%}
@media(max-width:880px){.adm-grid{grid-template-columns:repeat(2,1fr)}.adm-grid-3{grid-template-columns:repeat(2,1fr)}.adm-steps{grid-template-columns:repeat(2,1fr)}}
@media(max-width:560px){.adm-grid,.adm-grid-3{grid-template-columns:1fr}.review-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
@php
  $stepLabels = ['Course', 'Basic Info', 'Address', 'Review'];
@endphp

<div class="adm-shell">
  <div class="adm-header">
    <h2>Student Admission Wizard</h2>
    <p>Fill each step and review before submitting. The seat is booked on the final step.</p>
  </div>

  <form method="POST" action="{{ route('franchise.enrollment.store-new') }}" id="admission-form" autocomplete="off">
    @csrf
    <div class="adm-wrap">
      <div class="adm-card">

        {{-- Step indicators --}}
        <div class="adm-steps">
          @foreach($stepLabels as $i => $label)
            <div class="adm-step {{ $i === 0 ? 'active' : '' }}" data-indicator>{{ $label }}</div>
          @endforeach
        </div>

        <div class="adm-body">

          {{-- ── Step 1: Course ── --}}
          <div class="wizard-step active" data-step>
            <div class="adm-section-title">Course Setup</div>
            <div class="adm-section-note">Select a course type, duration, then choose the course.</div>
            <div class="adm-grid">
              <div class="gt-form-group">
                <label class="gt-label">Course Type <span style="color:var(--danger)">*</span></label>
                <select id="course_type_id" class="gt-select" required>
                  <option value="">Select Course Type</option>
                  @foreach($courseTypes as $ct)
                    <option value="{{ $ct->id }}">{{ $ct->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="gt-form-group">
                <label class="gt-label">Duration <span style="color:var(--danger)">*</span></label>
                <select id="course_duration_filter" class="gt-select" required>
                  <option value="">Select Duration</option>
                </select>
              </div>
              <div class="gt-form-group" style="grid-column:1/-1">
                <label class="gt-label">Course <span style="color:var(--danger)">*</span></label>
                <select name="course_id" id="course_id" class="gt-select" required style="display:none;">
                  <option value="">Select Course</option>
                </select>
                <div style="position:relative;">
                  <input type="text" id="course_search_display" class="gt-input"
                    placeholder="Search &amp; select course…" autocomplete="off"
                    style="width:100%;cursor:pointer;">
                  <div id="course_search_dropdown"
                    style="display:none;position:absolute;z-index:200;width:100%;top:calc(100% + 3px);background:var(--bg);border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>
                </div>
                @error('course_id')<div class="gt-error">{{ $message }}</div>@enderror
              </div>
              <div class="gt-form-group">
                <label class="gt-label">Batch</label>
                <select name="batch_id" class="gt-select">
                  <option value="">No Batch</option>
                  @foreach($batches as $b)
                    <option value="{{ $b->id }}" {{ old('batch_id') == $b->id ? 'selected' : '' }}>
                      {{ $b->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            {{-- Fee preview --}}
            <div class="fee-preview-box" id="fee-preview-box" style="display:none;">
              <div class="fee-preview-title">Fee Breakdown (Your Pricing)</div>
              <div id="fee-preview-lines"></div>
              <div class="fee-preview-total" id="fee-preview-total">Total: ₹0.00</div>
            </div>
          </div>

          {{-- ── Step 2: Basic Info ── --}}
          <div class="wizard-step" data-step>
            <div class="adm-section-title">Student Details</div>
            <div class="adm-section-note">Enter personal and guardian details for the student.</div>
            <div class="adm-grid">
              <div class="gt-form-group">
                <label class="gt-label">Student Name <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" id="inp-name" class="gt-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                  value="{{ old('name') }}" autocomplete="off" required>
                @error('name')<div class="gt-error">{{ $message }}</div>@enderror
              </div>
              <div class="gt-form-group">
                <label class="gt-label">Mobile <span style="color:var(--danger)">*</span></label>
                <input type="tel" name="mobile" id="mobile" class="gt-input {{ $errors->has('mobile') ? 'is-invalid' : '' }}"
                  value="{{ old('mobile') }}" autocomplete="off" required
                  maxlength="10" inputmode="numeric"
                  oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
                <div id="mobile-error" style="display:none;color:var(--danger);font-size:12px;margin-top:3px;"></div>
                @error('mobile')<div class="gt-error">{{ $message }}</div>@enderror
              </div>
              <div class="gt-form-group">
                <label class="gt-label">Email</label>
                <input type="email" name="email" class="gt-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                  value="{{ old('email') }}" autocomplete="off">
                @error('email')<div class="gt-error">{{ $message }}</div>@enderror
              </div>
              <div class="gt-form-group">
                <label class="gt-label">Date of Birth</label>
                <input type="date" name="dob" class="gt-input" value="{{ old('dob') }}">
              </div>
              <div class="gt-form-group">
                <label class="gt-label">Gender</label>
                <select name="gender" class="gt-select">
                  <option value="">Select</option>
                  <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                  <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>
              <div class="gt-form-group">
                <label class="gt-label">Father's Name</label>
                <input type="text" name="father_name" class="gt-input" value="{{ old('father_name') }}" placeholder="Father ka naam">
              </div>
            </div>
          </div>

          {{-- ── Step 3: Address ── --}}
          <div class="wizard-step" data-step>
            <div class="adm-section-title">Address Details</div>
            <div class="adm-section-note">Enter the student's current address information.</div>
            <div class="adm-grid">
              <div class="gt-form-group">
                <label class="gt-label">State</label>
                <select name="state" id="state-sel" class="gt-select">
                  <option value="">Select State</option>
                  @foreach($states as $s)
                    <option value="{{ $s }}" {{ old('state') === $s ? 'selected' : '' }}>{{ $s }}</option>
                  @endforeach
                </select>
              </div>
              <div class="gt-form-group">
                <label class="gt-label">District</label>
                <select name="district" id="district-sel" class="gt-select">
                  <option value="">Select District</option>
                  @if(old('district'))
                    <option value="{{ old('district') }}" selected>{{ old('district') }}</option>
                  @endif
                </select>
              </div>
              <div class="gt-form-group">
                <label class="gt-label">City</label>
                <input type="text" name="city" class="gt-input" value="{{ old('city') }}" placeholder="City / Town">
              </div>
              <div class="gt-form-group">
                <label class="gt-label">PIN Code</label>
                <input type="text" name="pin_code" class="gt-input" value="{{ old('pin_code') }}" placeholder="6-digit PIN" maxlength="10">
              </div>
              <div class="gt-form-group" style="grid-column:1/-1">
                <label class="gt-label">Full Address</label>
                <textarea name="address" class="gt-textarea" rows="2" placeholder="Street, locality, landmark…">{{ old('address') }}</textarea>
              </div>
            </div>
          </div>

          {{-- ── Step 4: Review ── --}}
          <div class="wizard-step" data-step>
            <div class="adm-section-title">Review &amp; Submit</div>
            <div class="adm-section-note">Verify all details before booking the seat.</div>
            <div class="review-grid">
              <div class="review-section">
                <div class="review-section-title">Course</div>
                <div class="review-row"><span>Course</span><strong id="rv-course">—</strong></div>
                <div class="review-row"><span>Duration</span><strong id="rv-duration">—</strong></div>
                <div class="review-row"><span>Total Fee</span><strong id="rv-fee" style="color:#ea580c;">—</strong></div>
              </div>
              <div class="review-section">
                <div class="review-section-title">Student Info</div>
                <div class="review-row"><span>Name</span><strong id="rv-name">—</strong></div>
                <div class="review-row"><span>Mobile</span><strong id="rv-mobile">—</strong></div>
                <div class="review-row"><span>Email</span><strong id="rv-email">—</strong></div>
                <div class="review-row"><span>DOB</span><strong id="rv-dob">—</strong></div>
                <div class="review-row"><span>Gender</span><strong id="rv-gender">—</strong></div>
                <div class="review-row"><span>Father</span><strong id="rv-father">—</strong></div>
              </div>
              <div class="review-section" style="grid-column:1/-1">
                <div class="review-section-title">Address</div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                  <div class="review-row" style="flex-direction:column;gap:2px;border-bottom:none;"><span style="color:var(--text-3);font-size:11px;">State</span><strong id="rv-state" style="color:var(--text-1);">—</strong></div>
                  <div class="review-row" style="flex-direction:column;gap:2px;border-bottom:none;"><span style="color:var(--text-3);font-size:11px;">District</span><strong id="rv-district" style="color:var(--text-1);">—</strong></div>
                  <div class="review-row" style="flex-direction:column;gap:2px;border-bottom:none;"><span style="color:var(--text-3);font-size:11px;">City</span><strong id="rv-city" style="color:var(--text-1);">—</strong></div>
                </div>
                <div class="review-row" style="margin-top:8px;"><span>PIN</span><strong id="rv-pin" style="color:var(--text-1);">—</strong></div>
                <div class="review-row" style="flex-direction:column;gap:4px;"><span>Address</span><strong id="rv-address" style="color:var(--text-1);font-size:12px;">—</strong></div>
              </div>
            </div>
          </div>

        </div>{{-- /.adm-body --}}

        {{-- Nav buttons --}}
        <div style="padding:0 24px 24px;" class="wizard-actions">
          <button type="button" id="btn-prev" class="btn btn-outline" style="display:none">← Previous</button>
          <div style="flex:1"></div>
          <button type="button" id="btn-next" class="btn btn-primary" style="background:#ea580c;border-color:#ea580c;">Next →</button>
          <button type="submit" id="btn-submit" class="btn btn-primary" style="display:none;background:#ea580c;border-color:#ea580c;">
            Book Seat →
          </button>
        </div>

      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const courseCatalog   = @json($courseCatalog);
  const districtsByState = @json($districtsByState);

  // ── Step wizard ─────────────────────────────────────────────────
  const steps      = Array.from(document.querySelectorAll('[data-step]'));
  const indicators = Array.from(document.querySelectorAll('[data-indicator]'));
  let   cur        = 0;

  function showStep(n) {
    steps.forEach((s, i) => s.classList.toggle('active', i === n));
    indicators.forEach((el, i) => {
      el.classList.toggle('active', i === n);
      el.classList.toggle('done', i < n);
    });
    document.getElementById('btn-prev').style.display   = n === 0 ? 'none' : '';
    document.getElementById('btn-next').style.display   = n === steps.length - 1 ? 'none' : '';
    document.getElementById('btn-submit').style.display = n === steps.length - 1 ? '' : 'none';
    if (n === steps.length - 1) populateReview();
    cur = n;
  }

  document.getElementById('btn-next').addEventListener('click', () => {
    if (!validateCurrentStep()) return;
    showStep(Math.min(cur + 1, steps.length - 1));
  });
  document.getElementById('btn-prev').addEventListener('click', () => showStep(Math.max(cur - 1, 0)));

  // ── Validation per step ─────────────────────────────────────────
  function validateCurrentStep() {
    const step = steps[cur];
    const required = step.querySelectorAll('[required]');
    let ok = true;
    required.forEach(el => {
      if (!el.value.trim()) {
        el.style.borderColor = 'var(--danger)';
        ok = false;
      } else {
        el.style.borderColor = '';
      }
    });
    if (!ok) { required[0]?.focus(); return false; }

    if (cur === 0) {
      if (!document.getElementById('course_id').value) {
        document.getElementById('course_search_display').style.borderColor = 'var(--danger)';
        return false;
      }
    }
    if (cur === 1) {
      const mob = document.getElementById('mobile');
      if (mob.value.length !== 10) {
        mob.style.borderColor = 'var(--danger)';
        document.getElementById('mobile-error').textContent = 'Mobile must be 10 digits.';
        document.getElementById('mobile-error').style.display = 'block';
        mob.focus(); return false;
      }
    }
    return true;
  }

  // ── Review populate ─────────────────────────────────────────────
  function val(id) { return document.getElementById(id)?.value || ''; }
  function sel(id) { const el = document.getElementById(id); return el?.options[el.selectedIndex]?.text || ''; }

  function populateReview() {
    const course = courseCatalog.find(c => String(c.id) === String(val('course_id')));
    document.getElementById('rv-course').textContent   = course ? course.name : '—';
    document.getElementById('rv-duration').textContent = course ? course.duration + ' months' : '—';
    document.getElementById('rv-fee').textContent      = course ? '₹' + Number(course.total_fee).toLocaleString('en-IN') : '—';
    document.getElementById('rv-name').textContent     = document.querySelector('[name=name]')?.value || '—';
    document.getElementById('rv-mobile').textContent   = val('mobile') || '—';
    document.getElementById('rv-email').textContent    = document.querySelector('[name=email]')?.value || '—';
    document.getElementById('rv-dob').textContent      = document.querySelector('[name=dob]')?.value || '—';
    document.getElementById('rv-gender').textContent   = document.querySelector('[name=gender]')?.value || '—';
    document.getElementById('rv-father').textContent   = document.querySelector('[name=father_name]')?.value || '—';
    document.getElementById('rv-state').textContent    = document.querySelector('[name=state]')?.value || '—';
    document.getElementById('rv-district').textContent = document.querySelector('[name=district]')?.value || '—';
    document.getElementById('rv-city').textContent     = document.querySelector('[name=city]')?.value || '—';
    document.getElementById('rv-pin').textContent      = document.querySelector('[name=pin_code]')?.value || '—';
    document.getElementById('rv-address').textContent  = document.querySelector('[name=address]')?.value || '—';
  }

  // ── Course type → duration → course cascade ─────────────────────
  const ctSel   = document.getElementById('course_type_id');
  const durSel  = document.getElementById('course_duration_filter');
  const crsId   = document.getElementById('course_id');
  const crsDisp = document.getElementById('course_search_display');
  const crsDrop = document.getElementById('course_search_dropdown');
  let   pool    = [];

  function renderDurations() {
    const tid = ctSel.value;
    const durs = [...new Set(
      courseCatalog.filter(c => String(c.course_type_id) === String(tid))
        .map(c => Number(c.duration)).filter(Boolean)
    )].sort((a,b) => a-b);
    durSel.innerHTML = '<option value="">Select Duration</option>' +
      durs.map(d => `<option value="${d}">${d} month${d===1?'':'s'}</option>`).join('');
  }

  function renderCourses() {
    const tid = ctSel.value, dur = durSel.value;
    pool = courseCatalog.filter(c =>
      String(c.course_type_id) === String(tid) && String(c.duration) === String(dur)
    );
    crsId.innerHTML = '<option value="">Select Course</option>' +
      pool.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    crsDisp.value = '';
    crsDrop.style.display = 'none';
  }

  function renderDropdown(q='') {
    const lower = q.trim().toLowerCase();
    const filt  = lower ? pool.filter(c => c.name.toLowerCase().includes(lower)) : pool;
    crsDrop.innerHTML = filt.length
      ? filt.map(c => `<div class="crs-opt" data-id="${c.id}" data-name="${c.name}"
            style="padding:9px 14px;font-size:13px;cursor:pointer;border-bottom:1px solid var(--border);">
            ${c.name} <span style="color:var(--text-3);font-size:11px;">(${c.duration}m)</span>
          </div>`).join('')
      : '<div style="padding:10px 14px;font-size:12px;color:var(--text-2);">No courses found</div>';
    crsDrop.querySelectorAll('.crs-opt').forEach(d => {
      d.addEventListener('mousedown', e => {
        e.preventDefault();
        crsId.value  = d.dataset.id;
        crsDisp.value = d.dataset.name;
        crsDrop.style.display = 'none';
        crsDisp.style.borderColor = '';
        renderFeePreview(d.dataset.id);
      });
    });
    crsDrop.style.display = 'block';
  }

  crsDisp.addEventListener('focus', () => pool.length && renderDropdown(crsDisp.value));
  crsDisp.addEventListener('input', () => { crsId.value = ''; renderDropdown(crsDisp.value); });
  crsDisp.addEventListener('blur',  () => setTimeout(() => crsDrop.style.display = 'none', 150));
  document.addEventListener('click', e => {
    if (!crsDrop.contains(e.target) && e.target !== crsDisp) crsDrop.style.display = 'none';
  });

  ctSel.addEventListener('change',  () => { renderDurations(); renderCourses(); });
  durSel.addEventListener('change', () => renderCourses());

  // ── Fee preview ─────────────────────────────────────────────────
  const feeBox   = document.getElementById('fee-preview-box');
  const feeLines = document.getElementById('fee-preview-lines');
  const feeTotal = document.getElementById('fee-preview-total');

  function renderFeePreview(id) {
    const c = courseCatalog.find(c => String(c.id) === String(id));
    if (!c || !c.fee_items?.length) { feeBox.style.display = 'none'; return; }
    feeLines.innerHTML = c.fee_items.map(f =>
      `<div class="fee-preview-line">
        <span style="color:var(--text-2);">${f.fee_type_name}</span>
        <span style="font-weight:600;">₹${Number(f.amount).toLocaleString('en-IN')}</span>
      </div>`
    ).join('');
    feeTotal.textContent = 'Total: ₹' + Number(c.total_fee).toLocaleString('en-IN');
    feeBox.style.display = 'block';
  }

  // ── District cascade ────────────────────────────────────────────
  const stateSel = document.getElementById('state-sel');
  const distSel  = document.getElementById('district-sel');

  function renderDistricts(state, selected='') {
    const dists = districtsByState[state] || [];
    distSel.innerHTML = '<option value="">Select District</option>' +
      dists.map(d => `<option value="${d}"${d===selected?' selected':''}>${d}</option>`).join('');
  }

  stateSel?.addEventListener('change', () => renderDistricts(stateSel.value));

  // Restore old district
  const oldState = '{{ old("state") }}', oldDist = '{{ old("district") }}';
  if (oldState && oldDist) renderDistricts(oldState, oldDist);

  // ── Mobile validation ──────────────────────────────────────────
  const mobInp = document.getElementById('mobile');
  mobInp?.addEventListener('blur', () => {
    const errEl = document.getElementById('mobile-error');
    if (mobInp.value && mobInp.value.length !== 10) {
      mobInp.style.borderColor = 'var(--danger)';
      errEl.textContent = 'Mobile must be exactly 10 digits.';
      errEl.style.display = 'block';
    } else {
      mobInp.style.borderColor = '';
      errEl.style.display = 'none';
    }
  });

  // ── Restore state on validation error (page reload) ─────────────
  @if($errors->any())
    // If there were errors, jump to step that has the error
    @if($errors->has('course_id'))
      showStep(0);
    @elseif($errors->hasAny(['name','mobile','email','dob','gender','father_name']))
      showStep(1);
    @else
      showStep(2);
    @endif
    // Restore course type / duration / course
    const oldCourseId = '{{ old("course_id") }}';
    const oldCourse = courseCatalog.find(c => String(c.id) === String(oldCourseId));
    if (oldCourse) {
      ctSel.value = String(oldCourse.course_type_id);
      renderDurations();
      durSel.value = String(oldCourse.duration);
      renderCourses();
      crsId.value  = oldCourseId;
      crsDisp.value = oldCourse.name;
      renderFeePreview(oldCourseId);
    }
  @endif
})();
</script>
@endpush
